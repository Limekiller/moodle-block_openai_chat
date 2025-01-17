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
 * API endpoint for retrieving GPT completion
 *
 * @package    block_openai_chat
 * @copyright  2023 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \block_openai_chat\completion;

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/blocks/openai_chat/lib.php');

global $DB, $PAGE;

if (get_config('block_openai_chat', 'restrictusage') !== "0") {
    require_login();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

$body = json_decode(file_get_contents('php://input'), true);
$message = clean_param($body['message'], PARAM_NOTAGS);
$history = clean_param_array($body['history'], PARAM_NOTAGS, true);
$block_id = clean_param($body['blockId'], PARAM_INT, true);
$thread_id = clean_param($body['threadId'], PARAM_NOTAGS, true);

// So that we're not leaking info to the client like API key, the block makes an API request including its ID
// Then we can look up that specific block to pull out its config data
$instance_record = $DB->get_record('block_instances', ['blockname' => 'openai_chat', 'id' => $block_id], '*');
$instance = block_instance('openai_chat', $instance_record);
if (!$instance) {
    print_error('invalidblockinstance', 'error', $id);
}

$context = context::instance_by_id($instance_record->parentcontextid);
$PAGE->set_context($context);

$block_settings = [];
$setting_names = [
    'sourceoftruth', 
    'prompt',
    'instructions',
    'username', 
    'assistantname', 
    'apikey', 
    'model', 
    'temperature', 
    'maxlength', 
    'topp', 
    'frequency', 
    'presence',
    'assistant'
];
foreach ($setting_names as $setting) {
    if ($instance->config && property_exists($instance->config, $setting)) {
        $block_settings[$setting] = $instance->config->$setting ? $instance->config->$setting : "";
    } else {
        $block_settings[$setting] = "";
    }
}

$engine_class;
$model = get_config('block_openai_chat', 'model');
$api_type = get_config('block_openai_chat', 'type');
$engine_class = "\block_openai_chat\completion\\$api_type";

$completion = new $engine_class(...[$model, $message, $history, $block_settings, $thread_id]);
$response = $completion->create_completion($PAGE->context);

// Format the markdown of each completion message into HTML.
$response["message"] = format_text($response["message"], FORMAT_MARKDOWN, ['context' => $context]);

// Log the message
log_message($message, $response['message'], $context);

$response = json_encode($response);
echo $response;
