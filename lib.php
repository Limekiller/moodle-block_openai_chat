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

function get_type_to_display() {
    $stored_type = get_config('block_openai_chat', 'type');
    if ($stored_type) {
        return $stored_type;
    }
    
    return 'chat';
}

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
            'OpenAI-Beta: assistants=v1'
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

function get_models() {
    return [
        "models" => [
            'gpt-4' => 'gpt-4',
            'gpt-4-1106-preview' => 'gpt-4-1106-preview',
            'gpt-4-0613' => 'gpt-4-0613',
            'gpt-4-0314' => 'gpt-4-0314',
            'gpt-3.5-turbo' => 'gpt-3.5-turbo',
            'gpt-3.5-turbo-16k-0613' => 'gpt-3.5-turbo-16k-0613',
            'gpt-3.5-turbo-16k' => 'gpt-3.5-turbo-16k',
            'gpt-3.5-turbo-1106' => 'gpt-3.5-turbo-1106',
            'gpt-3.5-turbo-0613' => 'gpt-3.5-turbo-0613',
            'gpt-3.5-turbo-0301' => 'gpt-3.5-turbo-0301',
        ],
        "types" => [
            'gpt-4' => 'chat',
            'gpt-4-1106-preview' => 'chat',
            'gpt-4-0613' => 'chat',
            'gpt-4-0314' => 'chat',
            'gpt-3.5-turbo' => 'chat',
            'gpt-3.5-turbo-16k-0613' => 'chat',
            'gpt-3.5-turbo-16k' => 'chat',
            'gpt-3.5-turbo-1106' => 'chat',
            'gpt-3.5-turbo-0613' => 'chat',
            'gpt-3.5-turbo-0301' => 'chat',
        ]
    ];
}