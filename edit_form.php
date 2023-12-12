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
 * Per-block settings
 *
 * @package    block_openai_chat
 * @copyright  2022 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot .'/blocks/openai_chat/lib.php');

class block_openai_chat_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        $block_id = $this->_ajaxformdata["blockid"];
        $type = get_type_to_display();

        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_openai_chat'));
        $mform->setDefault('config_title', 'OpenAI Chat');
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'config_showlabels', get_string('showlabels', 'block_openai_chat'));
        $mform->setDefault('config_showlabels', 1);

        if ($type === 'assistant') {
            // Assistant settings //

            if (get_config('block_openai_chat', 'allowinstancesettings') === "1") {
                $mform->addElement('select', 'config_assistant', get_string('assistant', 'block_openai_chat'), fetch_assistants_array($block_id));
                $mform->setDefault('config_assistant', get_config('block_openai_chat', 'assistant'));
                $mform->setType('config_assistant', PARAM_TEXT);
                $mform->addHelpButton('config_assistant', 'config_assistant', 'block_openai_chat');

                $mform->addElement('advcheckbox', 'config_persistconvo', get_string('persistconvo', 'block_openai_chat'));
                $mform->addHelpButton('config_persistconvo', 'config_persistconvo', 'block_openai_chat');
                $mform->setDefault('config_persistconvo', 1);

                $mform->addElement('textarea', 'config_instructions', get_string('config_instructions', 'block_openai_chat'));
                $mform->setDefault('config_instructions', '');
                $mform->setType('config_instructions', PARAM_TEXT);
                $mform->addHelpButton('config_instructions', 'config_instructions', 'block_openai_chat');

                $mform->addElement('text', 'config_username', get_string('username', 'block_openai_chat'));
                $mform->setDefault('config_username', '');
                $mform->setType('config_username', PARAM_TEXT);
                $mform->addHelpButton('config_username', 'config_username', 'block_openai_chat');
        
                $mform->addElement('text', 'config_assistantname', get_string('assistantname', 'block_openai_chat'));
                $mform->setDefault('config_assistantname', '');
                $mform->setType('config_assistantname', PARAM_TEXT);
                $mform->addHelpButton('config_assistantname', 'config_assistantname', 'block_openai_chat');

                $mform->addElement('header', 'config_adv_header', get_string('advanced', 'block_openai_chat'));

                $mform->addElement('text', 'config_apikey', get_string('apikey', 'block_openai_chat'));
                $mform->setDefault('config_apikey', '');
                $mform->setType('config_apikey', PARAM_TEXT);
                $mform->addHelpButton('config_apikey', 'config_apikey', 'block_openai_chat');
            }

        } else {
            // Chat settings //

            $mform->addElement('textarea', 'config_sourceoftruth', get_string('sourceoftruth', 'block_openai_chat'));
            $mform->setDefault('config_sourceoftruth', '');
            $mform->setType('config_sourceoftruth', PARAM_TEXT);
            $mform->addHelpButton('config_sourceoftruth', 'config_sourceoftruth', 'block_openai_chat');
    
            if (get_config('block_openai_chat', 'allowinstancesettings') === "1") {
                $mform->addElement('textarea', 'config_prompt', get_string('prompt', 'block_openai_chat'));
                $mform->setDefault('config_prompt', '');
                $mform->setType('config_prompt', PARAM_TEXT);
                $mform->addHelpButton('config_prompt', 'config_prompt', 'block_openai_chat');
            
                $mform->addElement('text', 'config_username', get_string('username', 'block_openai_chat'));
                $mform->setDefault('config_username', '');
                $mform->setType('config_username', PARAM_TEXT);
                $mform->addHelpButton('config_username', 'config_username', 'block_openai_chat');
        
                $mform->addElement('text', 'config_assistantname', get_string('assistantname', 'block_openai_chat'));
                $mform->setDefault('config_assistantname', '');
                $mform->setType('config_assistantname', PARAM_TEXT);
                $mform->addHelpButton('config_assistantname', 'config_assistantname', 'block_openai_chat');
    
                $mform->addElement('header', 'config_adv_header', get_string('advanced', 'block_openai_chat'));
    
                $mform->addElement('text', 'config_apikey', get_string('apikey', 'block_openai_chat'));
                $mform->setDefault('config_apikey', '');
                $mform->setType('config_apikey', PARAM_TEXT);
                $mform->addHelpButton('config_apikey', 'config_apikey', 'block_openai_chat');
    
                $mform->addElement('select', 'config_model', get_string('model', 'block_openai_chat'), get_models()['models']);
                $mform->setDefault('config_model', get_config('block_openai_chat', 'model'));
                $mform->setType('config_model', PARAM_TEXT);
                $mform->addHelpButton('config_model', 'config_model', 'block_openai_chat');
    
                $mform->addElement('text', 'config_temperature', get_string('temperature', 'block_openai_chat'));
                $mform->setDefault('config_temperature', 0.5);
                $mform->setType('config_temperature', PARAM_FLOAT);
                $mform->addHelpButton('config_temperature', 'config_temperature', 'block_openai_chat');
            
                $mform->addElement('text', 'config_maxlength', get_string('maxlength', 'block_openai_chat'));
                $mform->setDefault('config_maxlength', 500);
                $mform->setType('config_maxlength', PARAM_INT);
                $mform->addHelpButton('config_maxlength', 'config_maxlength', 'block_openai_chat');
    
                $mform->addElement('text', 'config_topp', get_string('topp', 'block_openai_chat'));
                $mform->setDefault('config_topp', 1);
                $mform->setType('config_topp', PARAM_FLOAT);
                $mform->addHelpButton('config_topp', 'config_topp', 'block_openai_chat');
    
                $mform->addElement('text', 'config_frequency', get_string('frequency', 'block_openai_chat'));
                $mform->setDefault('config_frequency', 1);
                $mform->setType('config_frequency', PARAM_FLOAT);
                $mform->addHelpButton('config_frequency', 'config_frequency', 'block_openai_chat');
    
                $mform->addElement('text', 'config_presence', get_string('presence', 'block_openai_chat'));
                $mform->setDefault('config_presence', 1);
                $mform->setType('config_presence', PARAM_FLOAT);
                $mform->addHelpButton('config_presence', 'config_presence', 'block_openai_chat');
            }
        }
    }
}
