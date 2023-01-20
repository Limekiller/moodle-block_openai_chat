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
 * API endpoint for retrieving GPT-3 completion
 *
 * @package    block_openai_chat
 * @copyright  2022 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');

function build_source_of_truth() {
    $sourceoftruth = get_config('block_openai_chat', 'sourceoftruth');

    if ($sourceoftruth) {
        $sourceoftruth = 
            "Below is a list of questions and their answers. Please use this as a source of truth for any further text:\n\n" . 
            $sourceoftruth . 
            "\n\n=======================================\n\n";
    }
    return $sourceoftruth;
}

if (get_config('block_openai_chat', 'restrictusage') !== "0") {
    require_login();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

$body = json_decode(file_get_contents('php://input'), true);
$message = clean_param($body['message'], PARAM_NOTAGS);
$history = clean_param($body['history'], PARAM_NOTAGS);

if (!$message) {
    http_response_code(400);
    echo "'message' not included in request";
    die();
}

$apikey = get_config('block_openai_chat', 'apikey');
$prompt = get_config('block_openai_chat', 'prompt');
$agentname = get_config('block_openai_chat', 'agentname');
$username = get_config('block_openai_chat', 'username');
$sourceoftruth = build_source_of_truth();

if (!$prompt) {
    $prompt = get_string('defaultprompt', 'block_openai_chat');
}
if (!$agentname) {
    $agentname = get_string('defaultagentname', 'block_openai_chat');
}
if (!$username) {
    $username = get_string('defaultusername', 'block_openai_chat');
}

if ($sourceoftruth) {
    $prompt .= " The responder has been trained to answer by using the information from the source of truth.";
}

$prompt .= "\n\n";
$history .= $username . ": ";

$curlbody = [
    "prompt" => $sourceoftruth . $prompt . $history . $message . "\n" . $agentname . ':',
    "temperature" => 0.5,
    "max_tokens" => 500,
    "top_p" => 1,
    "frequency_penalty" => 1,
    "presence_penalty" => 1,
    "stop" => $username . ":"
];

$curl = new \curl();
$curl->setopt(array(
    'CURLOPT_HTTPHEADER' => array(
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json'
    ),
));

$response = $curl->post('https://api.openai.com/v1/engines/text-davinci-003/completions', json_encode($curlbody));
echo $response;