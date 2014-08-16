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
 * @package   Storyplayer/OutputLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer;

use DataSift\Storyplayer\PlayerLib\PhaseGroup_Result;
use DataSift\Storyplayer\PlayerLib\Story_Result;
use DataSift\Storyplayer\OutputLib\OutputPlugin;
use DataSift\Storyplayer\Console\DefaultConsole;

/**
 * all output goes through here
 *
 * @category  Libraries
 * @package   Storyplayer/OutputLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class Output extends OutputPlugin
{
	protected $plugins = array();

	protected $activityLog = [];

	public function __construct()
	{
		// we need a default output for the console
		$this->plugins['console'] = new DefaultConsole();
		$this->plugins['console']->addOutputToStdout();
	}

	/**
	 * set the plugin for a named output slot
	 *
	 * @param string       $slot
	 *        the name of the slot to use for this plugin
	 * @param OutputPlugin $plugin
	 *        the plugin to use in the slot
	 */
	public function usePlugin($slot, OutputPlugin $plugin)
	{
		$this->plugins[$slot] = $plugin;
	}

	public function resetSilent()
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->resetSilent();
		}
	}

	public function setSilent()
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->setSilent();
		}
	}

	public function disableColourSupport()
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->disableColourSupport();
		}
	}

	public function enforceColourSupport()
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->enforceColourSupport();
		}
	}

	public function enableColourSupport()
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->enableColourSupport();
		}
	}

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
		foreach ($this->plugins as $plugin)
		{
			$plugin->startStoryplayer($version, $url, $copyright, $license);
		}
	}

	/**
	 * called when Storyplayer exits
	 *
	 * @return void
	 */
	public function endStoryplayer()
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->endStoryplayer();
		}
	}

	public function startPhaseGroup($activity, $name)
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->startPhaseGroup($activity, $name);
		}
	}

	public function endPhaseGroup($result)
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->endPhaseGroup($result);
		}
	}

	/**
	 * called when a story starts a new phase
	 *
	 * @return void
	 */
	public function startPhase($phase)
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->startPhase($phase);
		}
	}

	/**
	 * called when a story ends a phase
	 *
	 * @return void
	 */
	public function endPhase($phase, $phaseResult)
	{
		// inject the captured activity into the phase
		$phaseResult->activityLog = $this->activityLog;
		$this->activityLog=[];

		// pass the phase on
		foreach ($this->plugins as $plugin)
		{
			$plugin->endPhase($phase, $phaseResult);
		}
	}

	/**
	 * called when a story logs an action
	 *
	 * @param string $msg
	 * @return void
	 */
	public function logPhaseActivity($msg)
	{
		// keep track of what was attempted, in case we need to show
		// the user what was attempted
		$this->activityLog[] = [
			'ts'    => time(),
			'text'  => $msg
		];

		foreach ($this->plugins as $plugin)
		{
			$plugin->logPhaseActivity($msg);
		}
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
		// keep track of what was attempted, in case we need to show
		// the user what was attempted
		$this->activityLog[] = [
			'ts'    => time(),
			'text'  => $msg
		];

		foreach ($this->plugins as $plugin)
		{
			$plugin->logPhaseError($phaseName, $msg);
		}
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
		// keep track of what was attempted, in case we need to show
		// the user what was attempted
		$this->activityLog[] = [
			'ts'    => time(),
			'text'  => $msg
		];

		foreach ($this->plugins as $plugin)
		{
			$plugin->logPhaseSkipped($phaseName, $msg);
		}
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
		foreach ($this->plugins as $plugin)
		{
			$plugin->logCliError($msg);
		}
	}

	/**
	 * called when the outer CLI shell encounters a fatal error
	 *
	 * @param  string $msg
	 *         the error message to show the user
	 *
	 * @return void
	 */
	public function logCliErrorWithException($msg, $e)
	{
		foreach ($this->plugins as $plugin)
		{
			$plugin->logCliErrorWithException($msg, $e);
		}
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
		foreach ($this->plugins as $plugin)
		{
			$plugin->logCliWarning($msg);
		}
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
		foreach ($this->plugins as $plugin)
		{
			$plugin->logCliInfo($msg);
		}
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
		foreach ($this->plugins as $plugin)
		{
			$plugin->logVardump($name, $var);
		}
	}
}