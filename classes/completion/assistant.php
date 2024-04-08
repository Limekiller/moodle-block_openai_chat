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
 * Class providing completions for assistant API
 *
 * @package    block_openai_chat
 * @copyright  2023 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace block_openai_chat\completion;

use block_openai_chat\completion;
defined('MOODLE_INTERNAL') || die;

class assistant extends \block_openai_chat\completion {

    private $thread_id;

    public function __construct($model, $message, $history, $block_settings, $thread_id) {
        parent::__construct($model, $message, $history, $block_settings);

        // If thread_id is NULL, create a new thread
        if (!$thread_id) {
            $thread_id = $this->create_thread();
        }
        $this->thread_id = $thread_id;
    }

    /**
     * Given everything we know after constructing the parent, create a completion by constructing the prompt and making the api call
     * @return JSON: The API response from OpenAI
     */
    public function create_completion($context) {
        $this->add_message_to_thread();
        return $this->run();
    }

    private function create_thread() {
        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_HTTPHEADER' => array(
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1'
            ),
        ));

        $response = $curl->post("https://api.openai.com/v1/threads");
        $response = json_decode($response);

        return $response->id;
    }

    private function add_message_to_thread() {
        $curlbody = [
            "role" => "user",
            "content" => $this->message
        ];

        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_HTTPHEADER' => array(
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1'
            ),
        ));

        $response = $curl->post(
            "https://api.openai.com/v1/threads/" . $this->thread_id ."/messages", 
            json_encode($curlbody)
        );
        $response = json_decode($response);

        return $response->id;
    }

    /**
     * Make the actual API call to OpenAI
     * @return JSON: The response from OpenAI
     */
    private function run() {

        $curlbody = [
            "assistant_id" => $this->assistant,
        ];
        if ($this->instructions) {
            $curlbody["instructions"] = $this->instructions;
        }

        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_HTTPHEADER' => array(
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1'
            ),
        ));

        $response = $curl->post(
            "https://api.openai.com/v1/threads/" . $this->thread_id . "/runs", 
            json_encode($curlbody)
        );
        $response = json_decode($response);

        if (isset($response->error)) {
            throw new \Exception($response->error->message);
        }

        $run_id = $response->id;
        $run_completed = false;
        while (!$run_completed) {
            $run_completed = $this->check_run_status($run_id);
            sleep(1);
        }

        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_HTTPHEADER' => array(
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1'
            ),
        ));
        $response = $curl->get("https://api.openai.com/v1/threads/" . $this->thread_id . '/messages');
        $response = json_decode($response);

        return [
            "id" => $response->data[0]->id,
            "message" => $response->data[0]->content[0]->text->value,
            "thread_id" => $response->data[0]->thread_id
        ];
    }

    private function check_run_status($run_id) {
        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_HTTPHEADER' => array(
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1'
            ),
        ));

        $response = $curl->get("https://api.openai.com/v1/threads/" . $this->thread_id . "/runs/" . $run_id);
        $response = json_decode($response);
        
        if ($response->status === 'completed') {
            return true;
        }
        return false;
    }
}
