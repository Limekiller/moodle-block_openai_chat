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

    protected $apikey;
    protected $message;
    protected $history;

    protected $assistantname;
    protected $username;
    protected $prompt;
    protected $sourceoftruth;
    protected $model;
    protected $temperature;
    protected $maxlength;
    protected $topp;
    protected $frequency;
    protected $presence;

    protected $assistant;
    protected $instructions;

    /**
     * Initialize all the class properties that we'll need regardless of model
     * @param string model: The name of the model we're using
     * @param string message: The most recent message sent by the user
     * @param array history: An array of objects containing the history of the conversation
     * @param string block_settings: An object containing the instance-level settings if applicable
     */
    public function __construct($model, $message, $history, $block_settings) {
        // Set default values
        $this->model = $model;
        $this->apikey = get_config('block_openai_chat', 'apikey');

        // We fetch defaults for both chat and assistant APIs, even though only one can be active at a time
        // In the past, multiple different completion classes shared API types, so this might happen again
        // Any settings that don't apply to the current API type are just ignored

        $this->prompt = $this->get_setting('prompt', get_string('defaultprompt', 'block_openai_chat'));
        $this->assistantname = $this->get_setting('assistantname', get_string('defaultassistantname', 'block_openai_chat'));
        $this->username = $this->get_setting('username', get_string('defaultusername', 'block_openai_chat'));

        $this->temperature = $this->get_setting('temperature', 0.5);
        $this->maxlength = $this->get_setting('maxlength', 500);
        $this->topp = $this->get_setting('topp', 1);
        $this->frequency = $this->get_setting('frequency', 1);
        $this->presence = $this->get_setting('presence', 1);

        $this->assistant = $this->get_setting('assistant');
        $this->instructions = $this->get_setting('instructions');

        // Then override with block settings if applicable
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

    /**
     * Attempt to get the saved value for a setting; if this isn't set, return a passed default instead
     * @param string settingname: The name of the setting to fetch
     * @param mixed default_value: The default value to return if the setting isn't already set
     * @return mixed: The saved or default value
     */
    protected function get_setting($settingname, $default_value = null) {
        $setting = get_config('block_openai_chat', $settingname);
        if (!$setting && $setting != 0) {
            $setting = $default_value;
        }
        return $setting;
    }

    /**
     * Make the source of truth ready to add to the prompt by appending some extra information
     * @param string localsourceoftruth: The instance-level source of truth we got from the API call 
     */
    private function build_source_of_truth($localsourceoftruth) {
        $sourceoftruth = get_config('block_openai_chat', 'sourceoftruth');
    
        if ($sourceoftruth || $localsourceoftruth) {
            $sourceoftruth = 
                get_string('sourceoftruthpreamble', 'block_openai_chat')
                . $sourceoftruth . "\n\n"
                . $localsourceoftruth . "\n\n";
            }
        $this->sourceoftruth = $sourceoftruth;
    }
}