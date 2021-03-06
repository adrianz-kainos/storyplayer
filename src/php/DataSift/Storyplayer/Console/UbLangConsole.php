<?php

/**
 * Copyright (c) 2011-present Mediasift Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Storyplayer/Console
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\Console;

use DataSift\Storyplayer\OutputLib\CodeFormatter;
use DataSift\Storyplayer\Phases\Phase;
use DataSift\Storyplayer\PlayerLib\Phase_Result;
use DataSift\Storyplayer\PlayerLib\PhaseGroup_Result;
use DataSift\Storyplayer\PlayerLib\Story_Result;
use DataSift\Storyplayer\PlayerLib\Story;

/**
 * the console plugin we use to show the user how each story is going
 *
 * @category  Libraries
 * @package   Storyplayer/Console
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class UbLangConsole extends Console
{
    protected $currentPhase;
    protected $currentPhaseStep;
    protected $phaseGroupHasOutput = false;
    protected $phaseNumber = 0;
    protected $phaseMessages = array();

    /**
     * a list of the results we have received from stories
     * @var array
     */
    protected $storyResults = [];

    /**
     * called when storyplayer starts
     *
     * @param string $version
     * @param string $url
     * @param string $copyright
     * @param string $license
     * @return void
     */
    public function startStoryplayer($version, $url, $copyright, $license)
    {
        $this->write("Storyplayer {$version}", $this->writer->highlightStyle);
        $this->write(" - ");
        $this->write($url, $this->writer->urlStyle);
        $this->write(PHP_EOL);
        $this->write($copyright . PHP_EOL);
        $this->write($license . PHP_EOL . PHP_EOL);
    }

    public function endStoryplayer($duration)
    {
        $this->writeFinalReport($duration, true);
    }

    /**
     * called when we start a new set of phases
     *
     * @param  string $activity
     *         what are we doing? (e.g. 'creating', 'running')
     * @param  string $name
     *         the name of the phase group
     * @param  array|null $details
     *         optional explanation of what this PhaseGroup is trying
     *         to achieve
     * @return void
     */
    public function startPhaseGroup($activity, $name, $details = null)
    {
        $this->write($activity . ' ', $this->writer->activityStyle);
        $this->write($name, $this->writer->nameStyle);
        $this->write(' ...' . PHP_EOL, $this->writer->punctuationStyle);

        if (is_array($details)) {
            $this->write('  Scenario: ' . PHP_EOL, $this->writer->stepStyle);
            foreach ($details as $line) {
                $this->write('    ' . $line . PHP_EOL);
            }
        }

        // reset our tracker of any output
        $this->phaseGroupHasOutput = false;
    }

    public function endPhaseGroup($result)
    {
        // tell the user what happened
        $this->write('  Result: ');
        if ($result->getPhaseGroupSkipped()) {
            $this->writePhaseGroupSkipped($result->getResultString());
        }
        else if ($result->getPhaseGroupSucceeded()) {
            $this->writePhaseGroupSucceeded($result->getResultString());
        }
        else {
            $this->writePhaseGroupFailed($result->getResultString());
        }

        // write out the duration too
        $this->write(' (', $this->writer->punctuationStyle);
        $this->writeDuration($result->getDuration());
        $this->write(')' . PHP_EOL, $this->writer->punctuationStyle);

        // remember the result for the final report
        //
        // we have to clone as the result object apparently changes
        // afterwards. no idea why (yet)
        $this->results[] = clone $result;

        // if we are not connected to a terminal, we need to write out
        // a detailed error report
        if (!function_exists("posix_isatty") || !posix_isatty(STDOUT)) {
            $this->writeDetailedErrorReport($result);
        }
    }

    /**
     * called when a story starts a new phase
     *
     * @return void
     */
    public function startPhase($phase)
    {
        // shorthand
        $phaseType  = $phase->getPhaseType();
        $phaseSeqNo = $phase->getPhaseSequenceNo();

        $this->currentPhaseType = $phaseType;

        // we're only interested in telling the user about the
        // phases of a story
        if ($phaseType !== Phase::STORY_PHASE) {
            return;
        }

        // remember the phase for later use
        $this->currentPhase = $phaseSeqNo;
        $this->currentPhaseName = $phase->getPhaseName();
        $this->currentPhaseStep = 0;
    }

    /**
     * called when a story ends a phase
     *
     * @return void
     */
    public function endPhase($phase, $phaseResult)
    {
        // shorthand
        $phaseType = $phase->getPhaseType();

        // we're only interested in telling the user about the
        // phases of a story
        if ($phaseType !== Phase::STORY_PHASE) {
            return;
        }

        // if there was no output for the phase, skip the report
        if ($this->currentPhaseStep === 0) {
            return;
        }

        if ($phaseResult->getPhaseFailed() || $phaseResult->getPhaseHasErrored()) {
            $this->writePhaseGroupFailed($phaseResult->getPhaseResultString());
        }
        else if ($phaseResult->getPhaseIsIncomplete() || $phaseResult->getPhaseIsBlacklisted()) {
            $this->writePhaseGroupSkipped( $phaseResult->getPhaseResultString());
        }
        $this->write(PHP_EOL);
    }

    /**
     * called when a story logs an action
     *
     * @param string $msg
     * @return void
     */
    public function logPhaseActivity($msg, $codeLine = null)
    {
        // has output been suppressed?
        if ($this->isSilent()) {
            return;
        }

        // we only want Story phases
        if ($this->currentPhaseType !== Phase::STORY_PHASE) {
            return;
        }

        // skip any empty messages (just in case)
        if (strlen($msg) === 0) {
            return;
        }

        // we only want the top-level messages
        if ($msg{0} == ' ') {
            return;
        }

        // special case - first message for a phase group
        if (!$this->phaseGroupHasOutput && !$this->getIsVerbose()) {
            $this->write('  Steps:' . PHP_EOL, $this->writer->stepStyle);
        }

        // special case - first message for a phase
        if ($this->currentPhaseStep === 0 && $this->getIsVerbose()) {
            $this->write('  ' . $this->currentPhaseName . ':' . PHP_EOL, $this->writer->stepStyle);
        }
        // special case - not the first message for a phase
        if ($this->currentPhaseStep !== 0) {
            $this->write(PHP_EOL);
        }

        $this->write('    ' . $this->currentPhase . chr(ord('a') + $this->currentPhaseStep) . '. ', $this->writer->stepStyle);
        $this->write($msg . ' ');

        $this->currentPhaseStep++;
        $this->phaseGroupHasOutput = true;
    }

    /**
     * called when a story logs the (possibly partial) output from
     * running a subprocess
     *
     * @param  string $msg the output to log
     * @return void
     */
    public function logPhaseSubprocessOutput($msg)
    {
        // no-op?
    }

    /**
     * called when a story logs an error
     *
     * @param string $phaseName
     * @param string $msg
     * @return void
     */
    public function logPhaseError($phaseName, $msg)
    {
        // no-op?
    }

    /**
     * called when a story is skipped
     *
     * @param string $phaseName
     * @param string $msg
     * @return void
     */
    public function logPhaseSkipped($phaseName, $msg)
    {
        // no-op?
    }

    public function logPhaseCodeLine($codeLine)
    {
        // this is a no-op for us
    }

    /**
     * called when the outer CLI shell encounters a fatal error
     *
     * @param  string $msg
     *         the error message to show the user
     *
     * @return void
     */
    public function logCliError($msg)
    {
        $this->write("*** error: $msg" . PHP_EOL);
    }

    /**
     *
     * @param  string $msg
     * @param  Exception $e
     * @return void
     */
    public function logCliErrorWithException($msg, $e)
    {
        $this->write("*** error: $msg" . PHP_EOL . PHP_EOL
             . "This was caused by an unexpected exception " . get_class($e) . PHP_EOL . PHP_EOL
             . $e->getTraceAsString() . PHP_EOL);
    }

    /**
     * called when the outer CLI shell needs to publish a warning
     *
     * @param  string $msg
     *         the warning message to show the user
     *
     * @return void
     */
    public function logCliWarning($msg)
    {
        $this->write("*** warning: $msg" . PHP_EOL);
    }

    /**
     * called when the outer CLI shell needs to tell the user something
     *
     * @param  string $msg
     *         the message to show the user
     *
     * @return void
     */
    public function logCliInfo($msg)
    {
        $this->write($msg . PHP_EOL);
    }

    /**
     * an alternative to using PHP's built-in var_dump()
     *
     * @param  string $name
     *         a human-readable name to describe $var
     *
     * @param  mixed $var
     *         the variable to dump
     *
     * @return void
     */
    public function logVardump($name, $var)
    {
        // this is a no-op for us
    }
}
