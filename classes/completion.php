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
 * Base completion object class
 *
 * @package    block_openai_chat
 * @copyright  2023 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace block_openai_chat;
defined('MOODLE_INTERNAL') || die;

class completion {

    private $message;
    private $history;

    private $assistantname;
    private $username;
    private $prompt;
    private $sourceoftruth;

    /**
     * Initialize all the class properties that we'll need regardless of model
     * @param string model: The name of the model we're using
     * @param string message: The most recent message sent by the user
     * @param array history: An array of objects containing the history of the conversation
     * @param string block_settings: An object containing the instance-level settings if applicable
     */
    public function __construct($message, $history, $block_settings) {
        $this->prompt = $this->get_setting('prompt', get_string('defaultprompt', 'block_openai_chat'));
        $this->assistantname = $this->get_setting('assistantname', get_string('defaultassistantname', 'block_openai_chat'));
        $this->username = $this->get_setting('username', get_string('defaultusername', 'block_openai_chat'));

        // Override with block settings if applicable
        if (get_config('block_openai_chat', 'allowinstancesettings') === "1") {
            foreach ($block_settings as $name => $value) {
                if ($value) {
                    $this->$name = $value;
                }
            }
        }

        $this->message = $message;
        $this->history = $history;
        $this->build_source_of_truth($block_settings['sourceoftruth']);
    }

    public function create_completion($context) {
        global $USER;

        $historystring = $this->build_history_string();

        $fullprompt = 
            $this->sourceoftruth . 
            "----- SYSTEM PROMPT -----\n" .
            $this->prompt . "\n" . 
            "----- END SYSTEM PROMPT -----\n" .
            "----- BEGIN CONVERSATION -----\n" .
            $historystring . 
            $this->username . ": " . $this->message .
            $this->assistantname . ": ";

        $manager = \core\di::get(\core_ai\manager::class);
        $action = new \core_ai\aiactions\generate_text(
            contextid: $context->id,
            userid: $USER->id,
            prompttext: $fullprompt
        );

        $response = $manager->process_action($action);
        if (!$response->get_success()) {
            return [
                "result" => "error",
                "message" => $response->get_error() . ": " . $response->get_errormessage()
            ];
        }

        $responsedata = $response->get_response_data();
        $content = $responsedata['generatedcontent'] ?? '';
        return [
            "result" => "success",
            "message" => $content   
        ];
    }

    /**
     * Attempt to get the saved value for a setting; if this isn't set, return a passed default instead
     * @param string settingname: The name of the setting to fetch
     * @param mixed default_value: The default value to return if the setting isn't already set
     * @return mixed: The saved or default value
     */
    private function get_setting($settingname, $default_value = null) {
        $setting = get_config('block_openai_chat', $settingname);
        if (!$setting && $setting != 0) {
            $setting = $default_value;
        }
        return $setting;
    }

    private function build_history_string() {
        $string = "";
        foreach ($this->history as $message) {
            $string .= "{$message['user']}: {$message['message']}\n";
        }
        return $string;
    }

    /**
     * Make the source of truth ready to add to the prompt by appending some extra information
     * @param string localsourceoftruth: The instance-level source of truth we got from the API call 
     */
    private function build_source_of_truth($localsourceoftruth) {
        $sourceoftruth = get_config('block_openai_chat', 'sourceoftruth');
        if (!empty($sourceoftruth) || !empty($localsourceoftruth)) {
            $sourceoftruth = 
                get_string('sourceoftruthpreamble', 'block_openai_chat')
                . $sourceoftruth . "\n\n"
                . $localsourceoftruth . "\n";
        }
        
        $this->sourceoftruth =
            "----- SYSTEM INSTRUCTIONS ----\n" .
            "$sourceoftruth" .
            "----- END SYSTEM INSTRUCTIONS -----\n"
        ;
    }
}