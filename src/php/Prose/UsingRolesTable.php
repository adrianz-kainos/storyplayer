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
 * @package   Storyplayer/Prose
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace Prose;

use DataSift\Stone\ObjectLib\BaseObject;

/**
 * manipulate the internal roles table
 *
 * @category  Libraries
 * @package   Storyplayer/Prose
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class UsingRolesTable extends Prose
{
	/**
	 * entryKey
	 * The key that this table interacts with in the RuntimeConfig
	 *
	 * @var string
	 */
	protected $entryKey = "roles";

	/**
	 * addHost
	 *
	 * @param string $hostDetails
	 *        Details about the host to add to the role
	 * @param string $roleName
	 *        Role name to add
	 *
	 * @return void
	 */
	public function addHostToRole($hostDetails, $roleName)
	{
		// shorthand
		$st = $this->st;
		$hostId = $hostDetails->hostId;

		// what are we doing?
		$log = $st->startAction("add host '{$hostId}' to role '{$roleName}'");

		// add it
		$st->usingRuntimeTableForTargetEnvironment($this->entryKey)->addItemToGroup($roleName, $hostId, $hostDetails);

		// all done
		$log->endAction();
	}

	/**
	 * removeRole
	 *
	 * @param string $hostId
	 *        ID of the host to remove
	 * @param string $roleName
	 *        Role name to remove
	 *
	 * @return void
	 */
	public function removeHostFromRole($hostId, $roleName)
	{
		// shorthand
		$st = $this->st;

		// what are we doing?
		$log = $st->startAction("remove host '{$hostId}' from '{$roleName}'");

		// remove it
		$st->usingRuntimeTableForTargetEnvironment($this->entryKey)->removeItemFromGroup($roleName, $hostId);

		// all done
		$log->endAction();
	}

	public function removeHostFromAllRoles($hostId)
	{
		// shorthand
		$st = $this->st;

		// what are we doing?
		$log = $st->startAction("remove host '{$hostId}' from all roles");

		// remove it
		$st->usingRuntimeTableForTargetEnvironment($this->entryKey)->removeItemFromAllGroups($hostId);

		// all done
		$log->endAction();
	}
}