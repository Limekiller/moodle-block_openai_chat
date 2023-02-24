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
            get_string('sourceoftruthpreamble', 'block_openai_chat')
            . $sourceoftruth 
            . "\n\n";
        }
    return $sourceoftruth;
}

function get_setting($settingname, $default_value) {
    $setting = get_config('block_openai_chat', $settingname);
    if (!$setting && (float) $setting != 0) {
        $setting = $default_value;
    }
    return $setting;
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
$prompt = get_setting('prompt', get_string('defaultprompt', 'block_openai_chat'));
$agentname = get_setting('agentname', get_string('defaultagentname', 'block_openai_chat'));
$username = get_setting('username', get_string('defaultusername', 'block_openai_chat'));

$sourceoftruth = build_source_of_truth();
if ($sourceoftruth) {
    $prompt .= get_string('sourceoftruthreinforcement', 'block_openai_chat');
}

$prompt .= "\n\n";
$history .= $username . ": ";

$model = get_setting('model', 'text-davinci-003');
$temperature = get_setting('temperature', 0.5);
$maxlength = get_setting('maxlength', 500);
$topp = get_setting('topp', 1);
$frequency = get_setting('frequency', 1);
$presence = get_setting('presence', 1);

$curlbody = [
    "prompt" => $sourceoftruth . $prompt . $history . $message . "\n" . $agentname . ':',
    "temperature" => (float) $temperature,
    "max_tokens" => (int) $maxlength,
    "top_p" => (float) $topp,
    "frequency_penalty" => (float) $frequency,
    "presence_penalty" => (float) $presence,
    "stop" => $username . ":"
];

$curl = new \curl();
$curl->setopt(array(
    'CURLOPT_HTTPHEADER' => array(
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json'
    ),
));

$response = $curl->post("https://api.openai.com/v1/engines/$model/completions", json_encode($curlbody));
echo $response;