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
 * @package   Storyplayer/Modules/Supervisor
 * @author    Shweta Saikumar <shweta.saikumar@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace Storyplayer\SPv3\Modules\Supervisor;

use DataSift\Storyplayer\HostLib;
use DataSift\Storyplayer\OsLib;
use DataSift\Storyplayer\PlayerLib\StoryTeller;

use DataSift\Stone\ObjectLib\BaseObject;

use Storyplayer\SPv3\Modules\Exceptions;
use Storyplayer\SPv3\Modules\Host;
use Storyplayer\SPv3\Modules\Host\HostAwareModule;
use Storyplayer\SPv3\Modules\Log;
use Storyplayer\SPv3\Modules\Shell;

/**
 * start and stop programs under supervisor
 *
 * @category  Libraries
 * @package   Storyplayer/Modules/Supervisor
 * @author    Shweta Saikumar <shweta.saikumar@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class UsingSupervisor extends HostAwareModule
{
    public function startProgram($programName)
    {
        // what are we doing?
        $log = Log::usingLog()->startAction("start program '{$programName}' on host '{$this->args[0]}'");

        // get the host details
        $hostDetails = $this->getHostDetails();

        // start the program
        $result = Shell::onHost($hostDetails->hostId)->runCommand("sudo supervisorctl start '{$programName}'");

        // did the command succeed?
        if ($result->didCommandFail()) {
            throw Exceptions::newActionFailedException(__METHOD__, "failed to start process '{$programName} (via supervisord)'");
        }

        // all done
        $log->endAction();
        return true;
    }

    public function stopProgram($programName)
    {
        // what are we doing?
        $log = Log::usingLog()->startAction("stop program '{$programName}' on host '{$this->args[0]}'");

        // get the host details
        $hostDetails = $this->getHostDetails();

        // stop the program
        $result = Shell::onHost($hostDetails->hostId)->runCommand("sudo supervisorctl stop '{$programName}'");

        // did the command succeed?
        if ($result->didCommandFail()) {
            throw Exceptions::newActionFailedException(__METHOD__, "failed to start process '{$programName} (via supervisord)'");
        }

        // all done
        $log->endAction();
        return true;
    }
}
