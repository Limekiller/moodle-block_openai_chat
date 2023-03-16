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
 * @copyright  2022 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configcheckbox(
    'block_openai_chat/restrictusage',
    get_string('restrictusage', 'block_openai_chat'),
    get_string('restrictusagedesc', 'block_openai_chat'),
    1
));

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
    "Below is a conversation between a user and a support assistant for a Moodle site, where users go for online learning.",
    PARAM_TEXT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/assistantname',
    get_string('assistantname', 'block_openai_chat'),
    get_string('assistantnamedesc', 'block_openai_chat'),
    'Assistant',
    PARAM_TEXT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/username',
    get_string('username', 'block_openai_chat'),
    get_string('usernamedesc', 'block_openai_chat'),
    'User',
    PARAM_TEXT
));

$settings->add(new admin_setting_configtextarea(
    'block_openai_chat/sourceoftruth',
    get_string('sourceoftruth', 'block_openai_chat'),
    get_string('sourceoftruthdesc', 'block_openai_chat'),
    '',
    PARAM_TEXT
));

// Advanced Settings //

$settings->add(new admin_setting_heading(
    'block_openai_chat/advanced', 
    get_string('advanced', 'block_openai_chat'),
    get_string('advanceddesc', 'block_openai_chat'),
));

$settings->add(new admin_setting_configselect(
    'block_openai_chat/model',
    get_string('model', 'block_openai_chat'),
    get_string('modeldesc', 'block_openai_chat'),
    'text-davinci-003',
    [
        'gpt-3.5-turbo' => 'gpt-3.5-turbo',
        'gpt-3.5-turbo-0301' => 'gpt-3.5-turbo-0301',
        'text-davinci-003' => 'text-davinci-003',
        'text-davinci-002' => 'text-davinci-002',
        'text-davinci-001' => 'text-davinci-001',
        'text-curie-001' => 'text-curie-001',
        'text-babbage-001' => 'text-babbage-001',
        'text-ada-001' => 'text-ada-001',
        'davinci' => 'davinci',
        'curie' => 'curie',
        'babbage' => 'babbage',
        'ada' => 'ada'
    ]
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/temperature',
    get_string('temperature', 'block_openai_chat'),
    get_string('temperaturedesc', 'block_openai_chat'),
    0.5,
    PARAM_FLOAT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/maxlength',
    get_string('maxlength', 'block_openai_chat'),
    get_string('maxlengthdesc', 'block_openai_chat'),
    500,
    PARAM_INT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/topp',
    get_string('topp', 'block_openai_chat'),
    get_string('toppdesc', 'block_openai_chat'),
    1,
    PARAM_FLOAT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/frequency',
    get_string('frequency', 'block_openai_chat'),
    get_string('frequencydesc', 'block_openai_chat'),
    1,
    PARAM_FLOAT
));

$settings->add(new admin_setting_configtext(
    'block_openai_chat/presence',
    get_string('presence', 'block_openai_chat'),
    get_string('presencedesc', 'block_openai_chat'),
    1,
    PARAM_FLOAT
));
