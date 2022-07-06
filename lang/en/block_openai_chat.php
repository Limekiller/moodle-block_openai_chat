<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language strings
 *
 * @package    block_openai_chat
 * @copyright  2022 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'OpenAI Chat Block';
$string['openai_chat'] = 'OpenAI Chat';
$string['openaichat:addinstance'] = 'Add a new OpenAI Chat block';
$string['openaichat:myaddinstance'] = 'Add a new OpenAI Chat block to the My Moodle page';

$string['blocktitle'] = 'Block title';

$string['apikey'] = 'OpenAI API Key';
$string['apikeydesc'] = 'The API Key for your OpenAI account';
$string['prompt'] = 'Completion prompt';
$string['promptdesc'] = 'The prompt the AI will be given before the conversation transcript.';
$string['agentname'] = 'Agent name';
$string['agentnamedesc'] = 'The name that the AI will use for itself internally';
$string['username'] = 'User name';
$string['usernamedesc'] = 'The name that the AI will use for the user internally';
$string['showlabels'] = 'Show labels';

$string['defaultprompt'] = "Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:";
$string['defaultagentname'] = 'Agent';
$string['defaultusername'] = 'User';
$string['askaquestion'] = 'Ask a question...';
$string['apikeymissing'] = 'Please add your OpenAI API key to the global block settings.';
