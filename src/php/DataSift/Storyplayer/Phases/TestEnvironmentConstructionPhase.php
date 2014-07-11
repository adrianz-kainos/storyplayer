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
 * @package   Storyplayer/Phases
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\Phases;

use Exception;
use DataSift\Storyplayer\HostLib;
use DataSift\Storyplayer\Prose\E5xx_ActionFailed;
use DataSift\Storyplayer\Prose\E5xx_ExpectFailed;
use DataSift\Storyplayer\Prose\E5xx_NotImplemented;

/**
 * the TestEnvironmentConstruction phase
 *
 * @category  Libraries
 * @package   Storyplayer/Phases
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

class TestEnvironmentConstructionPhase extends StoryPhase
{
	public function doPhase()
	{
		// shorthand
		$st = $this->st;

		// our return value
		$phaseResult = $this->getNewPhaseResult();

		// find out what we need to be doing
		$testEnvironmentConfig = (array)$st->getTestEnvironmentConfig();

		// are there any machines to create?
		if (empty($testEnvironmentConfig)) {
			// nothing to do
			$phaseResult->setContinuePlaying();
			return $phaseResult;
		}

		// create the machines
		try {
			foreach ($testEnvironmentConfig as $host) {
				$hostAdapter = HostLib::getHostAdapter($st, $host->type);
				$hostAdapter->createHost($host->details);
			}

			$phaseResult->setContinuePlaying();
			$phaseResult->addPairedPhase('TestEnvironmentDemolition');
		}
		catch (E5xx_ActionFailed $e) {
			$phaseResult->setPlayingFailed(
				$phaseResult::FAILED,
				$e->getMessage(),
				$e
			);
		}
		catch (E5xx_ExpectFailed $e) {
			$phaseResult->setPlayingFailed(
				$phaseResult::FAILED,
				$e->getMessage(),
				$e
			);
		}
		// if any of the tests are incomplete, deal with that too
		catch (E5xx_NotImplemented $e) {
			$phaseResult->setPlayingFailed(
				$phaseResult::INCOMPLETE,
				$e->getMessage(),
				$e
			);
		}
		catch (Exception $e) {
			$phaseResult->setPlayingFailed(
				$phaseResult::ERROR,
				$e->getMessage(),
				$e
			);
			$phaseResult->addPairedPhase('TestEnvironmentDemolition');
		}

		if (!$phaseResult->getPhaseSucceeded()) {
			var_dump($phaseResult->getMessage());
			var_dump($phaseResult->getException()->getTraceAsString());
			exit(1);
		}

		// all done
		return $phaseResult;
	}
}