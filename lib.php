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
 * General plugin functions
 *
 * @package    block_openai_chat
 * @copyright  2023 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Fetch the current API type from the database, defaulting to "chat"
 * @return String: the API type (chat|azure|assistant)
 */
function get_type_to_display() {
    $stored_type = get_config('block_openai_chat', 'type');
    if ($stored_type) {
        return $stored_type;
    }
    
    return 'chat';
}

/**
 * Use an API key to fetch a list of assistants from a user's OpenAI account
 * @param Int (optional): The ID of a block instance. If this is passed, the API can be pulled from the block rather than the site level.
 * @return Array: The list of assistants
 */
function fetch_assistants_array($block_id = null) {
    global $DB;

    if (!$block_id) {
        $apikey = get_config('block_openai_chat', 'apikey');
    } else {
        $instance_record = $DB->get_record('block_instances', ['blockname' => 'openai_chat', 'id' => $block_id], '*');
        $instance = block_instance('openai_chat', $instance_record);
        $apikey = $instance->config->apikey ? $instance->config->apikey : get_config('block_openai_chat', 'apikey');
    }

    if (!$apikey) {
        return [];
    }

    $curl = new \curl();
    $curl->setopt(array(
        'CURLOPT_HTTPHEADER' => array(
            'Authorization: Bearer ' . $apikey,
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2'
        ),
    ));

    $response = $curl->get("https://api.openai.com/v1/assistants?order=desc");
    $response = json_decode($response);
    $assistant_array = [];
    if (property_exists($response, 'data')) {
        foreach ($response->data as $assistant) {
            $assistant_array[$assistant->id] = $assistant->name;
        }
    }

    return $assistant_array;
}

/**
 * Return a list of available models, and the type of each model.
 * (Type used to be relevant before OpenAI released the Assistant API. Currently it is no longer useful as all models are of type "chat,"
 * but I left it here in case the API is changed significantly in the future)
 * @return Array: The list of model info
 */
function get_models() {
    return [
        "models" => [
         'meta-llama/Meta-Llama-3.1-8B-Instruct' => 'meta-llama/Meta-Llama-3.1-8B-Instruct',
         'meta-llama/Meta-Llama-3.1-70B-Instruct' => 'meta-llama/Meta-Llama-3.1-70B-Instruct'
        ],
        "types" => [
          'meta-llama/Meta-Llama-3.1-8B-Instruct' => 'chat',
          'meta-llama/Meta-Llama-3.1-70B-Instruct' => 'chat'
        ]
    ];
}

/**
 * If setting is enabled, log the user's message and the AI response
 * @param string usermessage: The text sent from the user
 * @param string airesponse: The text returned by the AI 
 */
function log_message($usermessage, $airesponse, $context) {
    global $USER, $DB;

    if (!get_config('block_openai_chat', 'logging')) {
        return;
    }

    $DB->insert_record('block_openai_chat_log', (object) [
        'userid' => $USER->id,
        'usermessage' => $usermessage,
        'airesponse' => $airesponse,
        'contextid' => $context->id,
        'timecreated' => time()
    ]);
}

function block_openai_chat_extend_navigation_course($nav, $course, $context) {
    if ($nav->get('coursereports')) {
        $nav->get('coursereports')->add(
            get_string('openai_chat_logs', 'block_openai_chat'),
            new moodle_url('/blocks/openai_chat/report.php', ['courseid' => $course->id]),
            navigation_node::TYPE_SETTING,
            null
        );
    }
}
