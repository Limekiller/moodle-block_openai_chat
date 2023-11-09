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
 * @copyright  2022 Bryce Yoder <me@bryceyoder.com>
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
$block_id = clean_param($body['blockID'], PARAM_INT, true);

// So that we're not leaking info to the client like API key, the block makes an API request including its ID
// Then we can look up that specific block to pull out its config data
$instance_record = $DB->get_record('block_instances', ['blockname' => 'openai_chat', 'id' => $block_id], '*');
$instance = block_instance('openai_chat', $instance_record);
if (!$instance) {
    print_error('invalidblockinstance', 'error', $id);
}

$context = context::instance_by_id($instance_record->parentcontextid);
if ($context->contextlevel == CONTEXT_COURSE) {
    $course = get_course($context->instanceid);
    $PAGE->set_course($course);
} else {
    $PAGE->set_context($context);
}

$block_settings = [];
$setting_names = [
    'sourceoftruth', 
    'prompt', 
    'username', 
    'assistantname', 
    'apikey', 
    'model', 
    'temperature', 
    'maxlength', 
    'topp', 
    'frequency', 
    'presence'
];
foreach ($setting_names as $setting) {
    if ($instance->config && property_exists($instance->config, $setting)) {
        $block_settings[$setting] = $instance->config->$setting ? $instance->config->$setting : "";
    } else {
        $block_settings[$setting] = "";
    }
}

$engines = get_models()['types'];
$model = get_config('block_openai_chat', 'model');
if (get_config('block_openai_chat', 'allowinstancesettings') === "1" && $block_settings['model']) {
    $model = $block_settings['model'];
}
if (!$model) {
    $model = 'text-davinci-003';
}

$engine_class = '\block_openai_chat\completion\\' . $engines[$model];
$completion = new $engine_class(...[$model, $message, $history, $block_settings]);
$response = $completion->create_completion($PAGE->context);

// Convert messages from Markdown to HTML.
// Decode the response
$response = json_decode($response);
// Format the markdown of each completion message into HTML.
foreach($response->choices as $key => $choice ) {
    if ($engines[$model] == 'chat') {
        $response->choices[$key]->message->content = format_text($choice->message->content, FORMAT_MARKDOWN, ['context' => $context]);
    } else {
        $response->choices[$key]->text = format_text($choice->text, FORMAT_MARKDOWN, ['context' => $context]);
    }
}
// Re-encode it the response.
$response = json_encode($response);

echo $response;
