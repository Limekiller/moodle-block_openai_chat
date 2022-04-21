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
 * Plugin settings
 *
 * @package    block_openai_chat
 * @copyright  2022 Bryce Yoder
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext(
    'block_openai_chat/apikey',
    get_string('apikey', 'block_openai_chat'),
    get_string('apikeydesc', 'block_openai_chat'),
    '',
    PARAM_TEXT
));

$settings->add(new admin_setting_configtextarea(
    'block_openai_chat/prompt',
    get_string('prompt', 'block_openai_chat'),
    get_string('promptdesc', 'block_openai_chat'),
    "Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:",
    PARAM_TEXT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/agentname',
    get_string('agentname', 'block_openai_chat'),
    get_string('agentnamedesc', 'block_openai_chat'),
    'Agent',
    PARAM_TEXT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/username',
    get_string('username', 'block_openai_chat'),
    get_string('usernamedesc', 'block_openai_chat'),
    'User',
    PARAM_TEXT
));
