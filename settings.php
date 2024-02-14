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

if ($hassiteconfig && $ADMIN->fulltree) {

    require_once($CFG->dirroot .'/blocks/openai_chat/lib.php');

    $type = get_type_to_display();
    $assistant_array = [];
    if ($type === 'assistant') {
        $assistant_array = fetch_assistants_array();
    }
    
    global $PAGE;
    $PAGE->requires->js_call_amd('block_openai_chat/settings', 'init');
    
    $settings->add(new admin_setting_configtext(
        'block_openai_chat/apikey',
        get_string('apikey', 'block_openai_chat'),
        get_string('apikeydesc', 'block_openai_chat'),
        '',
        PARAM_TEXT
    ));
    
    $settings->add(new admin_setting_configselect(
        'block_openai_chat/type',
        get_string('type', 'block_openai_chat'),
        get_string('typedesc', 'block_openai_chat'),
        'chat',
        ['chat' => 'chat', 'assistant' => 'assistant', 'azure' => 'azure']
    ));
    
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
    
    // Assistant settings //
    
    if ($type === 'assistant') {
        $settings->add(new admin_setting_heading(
            'block_openai_chat/assistantheading', 
            get_string('assistantheading', 'block_openai_chat'),
            get_string('assistantheadingdesc', 'block_openai_chat')
        ));
    
        if (count($assistant_array)) {
            $settings->add(new admin_setting_configselect(
                'block_openai_chat/assistant',
                get_string('assistant', 'block_openai_chat'),
                get_string('assistantdesc', 'block_openai_chat'),
                count($assistant_array) ? reset($assistant_array) : null,
                $assistant_array
            ));
        } else {
            $settings->add(new admin_setting_description(
                'block_openai_chat/noassistants',
                get_string('assistant', 'block_openai_chat'),
                get_string('noassistants', 'block_openai_chat'),
            ));    
        }
    
        $settings->add(new admin_setting_configcheckbox(
            'block_openai_chat/persistconvo',
            get_string('persistconvo', 'block_openai_chat'),
            get_string('persistconvodesc', 'block_openai_chat'),
            1
        ));
    
    } else {
    
        // Chat settings //
    
        if ($type === 'azure') {
            $settings->add(new admin_setting_heading(
                'block_openai_chat/azureheading', 
                get_string('azureheading', 'block_openai_chat'),
                get_string('azureheadingdesc', 'block_openai_chat')
            ));
    
            $settings->add(new admin_setting_configtext(
                'block_openai_chat/resourcename', 
                get_string('resourcename', 'block_openai_chat'),
                get_string('resourcenamedesc', 'block_openai_chat'),
                "",
                PARAM_TEXT
            ));
    
            $settings->add(new admin_setting_configtext(
                'block_openai_chat/deploymentid', 
                get_string('deploymentid', 'block_openai_chat'),
                get_string('deploymentiddesc', 'block_openai_chat'),
                "",
                PARAM_TEXT
            ));
    
            $settings->add(new admin_setting_configtext(
                'block_openai_chat/apiversion', 
                get_string('apiversion', 'block_openai_chat'),
                get_string('apiversiondesc', 'block_openai_chat'),
                "2023-09-01-preview",
                PARAM_TEXT
            ));
        }
    
        $settings->add(new admin_setting_heading(
            'block_openai_chat/chatheading', 
            get_string('chatheading', 'block_openai_chat'),
            get_string('chatheadingdesc', 'block_openai_chat')
        ));
    
        $settings->add(new admin_setting_configtextarea(
            'block_openai_chat/prompt',
            get_string('prompt', 'block_openai_chat'),
            get_string('promptdesc', 'block_openai_chat'),
            "Below is a conversation between a user and a support assistant for a Moodle site, where users go for online learning.",
            PARAM_TEXT
        ));
        
        $settings->add(new admin_setting_configtextarea(
            'block_openai_chat/sourceoftruth',
            get_string('sourceoftruth', 'block_openai_chat'),
            get_string('sourceoftruthdesc', 'block_openai_chat'),
            '',
            PARAM_TEXT
        ));
    }
    
    
    // Advanced Settings //
    
    $settings->add(new admin_setting_heading(
        'block_openai_chat/advanced', 
        get_string('advanced', 'block_openai_chat'),
        get_string('advanceddesc', 'block_openai_chat')
    ));
    
    $settings->add(new admin_setting_configcheckbox(
        'block_openai_chat/allowinstancesettings',
        get_string('allowinstancesettings', 'block_openai_chat'),
        get_string('allowinstancesettingsdesc', 'block_openai_chat'),
        0
    ));
    
    if ($type === 'assistant') {
    
    } else {
        $settings->add(new admin_setting_configselect(
            'block_openai_chat/model',
            get_string('model', 'block_openai_chat'),
            get_string('modeldesc', 'block_openai_chat'),
            'text-davinci-003',
            get_models()['models']
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
    }
}