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
 * @copyright  2024 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    if (!defined('BEHAT_SITE_RUNNING')) {
        $ADMIN->add('reports', new admin_externalpage(
            'openai_chat_report', 
            get_string('openai_chat_logs', 'block_openai_chat'), 
            new moodle_url("$CFG->wwwroot/blocks/openai_chat/report.php", ['courseid' => 1]),
            'moodle/site:config'
        ));
    }

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configcheckbox(
            'block_openai_chat/restrictusage',
            get_string('restrictusage', 'block_openai_chat'),
            get_string('restrictusagedesc', 'block_openai_chat'),
            1
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

        $settings->add(new admin_setting_configcheckbox(
            'block_openai_chat/logging',
            get_string('logging', 'block_openai_chat'),
            get_string('loggingdesc', 'block_openai_chat'),
            0
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_openai_chat/persistconvo',
            get_string('persistconvo', 'block_openai_chat'),
            get_string('persistconvodesc', 'block_openai_chat'),
            0
        ));

        $settings->add(new admin_setting_configtextarea(
            'block_openai_chat/prompt',
            get_string('prompt', 'block_openai_chat'),
            get_string('promptdesc', 'block_openai_chat'),
            get_string('defaultprompt', 'block_openai_chat'),
            PARAM_TEXT
        ));

        $settings->add(new admin_setting_configtextarea(
            'block_openai_chat/sourceoftruth',
            get_string('sourceoftruth', 'block_openai_chat'),
            get_string('sourceoftruthdesc', 'block_openai_chat'),
            '',
            PARAM_TEXT
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_openai_chat/allowinstancesettings',
            get_string('allowinstancesettings', 'block_openai_chat'),
            get_string('allowinstancesettingsdesc', 'block_openai_chat'),
            0
        ));
    }
}
