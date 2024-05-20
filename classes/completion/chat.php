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
 * Class providing completions for chat models (3.5 and up)
 *
 * @package    block_openai_chat
 * @copyright  2023 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace block_openai_chat\completion;

use block_openai_chat\completion;
defined('MOODLE_INTERNAL') || die;

class chat extends \block_openai_chat\completion {

    public function __construct($model, $message, $history, $block_settings, $thread_id = null) {
        parent::__construct($model, $message, $history, $block_settings);
    }

    /**
     * Given everything we know after constructing the parent, create a completion by constructing the prompt and making the api call
     * @return JSON: The API response from OpenAI
     */
    public function create_completion($context) {
        if ($this->sourceoftruth) {
            $this->sourceoftruth = format_string($this->sourceoftruth, true, ['context' => $context]);
            $this->prompt .= get_string('sourceoftruthreinforcement', 'block_openai_chat');
        }
        $this->prompt .= "\n\n";

        $history_json = $this->format_history();
        array_unshift($history_json, ["role" => "system", "content" => $this->prompt]);
        array_unshift($history_json, ["role" => "system", "content" => $this->sourceoftruth]);

        array_push($history_json, ["role" => "user", "content" => $this->message]);

        $response_data = $this->make_api_call($history_json);
        return $response_data;
    }

    /**
     * Format the history JSON into a string that we can pass in the prompt
     * @return string: The string representing the chat history to add to the prompt
     */
    protected function format_history() {
        $history = [];
        foreach ($this->history as $index => $message) {
            $role = $index % 2 === 0 ? 'user' : 'assistant';
            array_push($history, ["role" => $role, "content" => $message["message"]]);
        }
        return $history;
    }

    /**
     * Make the actual API call to OpenAI
     * @return JSON: The response from OpenAI
     */
    private function make_api_call($history) {
        $curlbody = [
            "model" => $this->model,
            "messages" => $history,
            "temperature" => (float) $this->temperature,
            "max_tokens" => (int) $this->maxlength,
            "top_p" => (float) $this->topp,
            "frequency_penalty" => (float) $this->frequency,
            "presence_penalty" => (float) $this->presence,
            "stop" => $this->username . ":"
        ];

        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_HTTPHEADER' => array(
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json'
            ),
        ));

        $response = $curl->post("https://api.openai.com/v1/chat/completions", json_encode($curlbody));
        $response = json_decode($response);

        $message = null;
        if (property_exists($response, 'error')) {
            $message = 'ERROR: ' . $response->error->message;
        } else {
            $message = $response->choices[0]->message->content;
        }

        return [
            "id" => property_exists($response, 'id') ? $response->id : 'error',
            "message" => $message
        ];
    }
}
