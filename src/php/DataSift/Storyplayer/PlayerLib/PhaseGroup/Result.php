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
 * @package   Storyplayer/PlayerLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\PlayerLib;

use DataSift\Storyplayer\StoryLib\Story;

/**
 * a record of what happened with a phase group
 *
 * @category  Libraries
 * @package   Storyplayer/PlayerLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class PhaseGroup_Result
{
	/**
	 *
	 * @var PhaseResult
	 */
	public $failedPhase     = null;

	/**
	 * did the story pass, fail, or otherwise go horribly wrong?
	 * @var integer
	 */
	public $resultCode     = 0;

	/**
	 * when did we start playing this story?
	 * @var float
	 */
	public $startTime       = null;

	/**
	 * when did we finish playing this story?
	 * @var float
	 */
	public $endTime         = null;

	/**
	 * how long did the story take to play?
	 * @var float
	 */
	public $durationTime    = null;

	const UNKNOWN     = 0;
	const OKAY        = 1;
	const FAIL        = 2;
	const ERROR       = 3;
	const INCOMPLETE  = 4;
	const BLACKLISTED = 5;

	public $resultStrings = [
		'UNKNOWN',
		'OKAY',
		'FAIL',
		'ERROR',
		'INCOMPLETE',
		'BLACKLISTED'
	];

	public function __construct()
	{
		// remember when we were created - we're going to treat that
		// as the start time for this story!
		$this->startTime = microtime(true);
	}

	public function setPhaseGroupHasSucceeded()
	{
		$this->resultCode  = self::OKAY;
		$this->failedPhase = null;
		$this->setEndTime();
	}

	public function setPhaseGroupHasBeenBlacklisted($phaseResult)
	{
		$this->resultCode  = self::BLACKLISTED;
		$this->failedPhase = $phaseResult;
		$this->setEndTime();
	}

	public function setPhaseGroupIsIncomplete($phaseResult)
	{
		$this->resultCode  = self::INCOMPLETE;
		$this->failedPhase = $phaseResult;
		$this->setEndTime();
	}

	public function setPhaseGroupHasFailed($phaseResult)
	{
		$this->resultCode  = self::FAIL;
		$this->failedPhase = $phaseResult;
		$this->setEndTime();
	}

	public function setPhaseGroupHasError($phaseResult)
	{
		$this->resultCode  = self::ERROR;
		$this->failedPhase = $phaseResult;
		$this->setEndTime();
	}

	public function getPhaseGroupSucceeded()
	{
		if ($this->resultCode = self::OKAY) {
			return true;
		}
		return false;
	}

	protected function setEndTime()
	{
		$this->endTime = microtime(true);
		$this->durationTime = $this->endTime - $this->startTime;
	}

	public function getDuration()
	{
		return $this->durationTime;
	}

	public function getResultString()
	{
		if (isset($this->resultStrings[$this->resultCode])) {
			return $this->resultStrings[$this->resultCode];
		}

		// either we don't have a string, or the result code itself is
		// an unexpected value
		return 'UNKNOWN';
	}
}
