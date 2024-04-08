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
 * Class providing completions for Azure
 *
 * @package    block_openai_chat
 * @copyright  2024 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace block_openai_chat\completion;

use block_openai_chat\completion;
defined('MOODLE_INTERNAL') || die;

class azure extends \block_openai_chat\completion\chat {

    private $resourcename;
    private $deploymentid;
    private $apiversion;

    public function __construct($model, $message, $history, $block_settings, $thread_id = null) {
        parent::__construct($model, $message, $history, $block_settings);

        $this->resourcename = $this->get_setting('resourcename');
        $this->deploymentid = $this->get_setting('deploymentid');
        $this->apiversion = $this->get_setting('apiversion');
    }

    /**
     * Given everything we know after constructing the parent, create a completion by constructing the prompt and making the api call
     * @return JSON: The API response from Azure
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
     * Make the actual API call to Azure
     * @return JSON: The response from Azure
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
                'api-key: ' . $this->apikey,
                'Content-Type: application/json'
            ),
        ));

        $response = $curl->post(
            "https://" . $this->resourcename . ".openai.azure.com/openai/deployments/" . $this->deploymentid . "/chat/completions?api-version=" . $this->apiversion, 
            json_encode($curlbody)
        );
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
