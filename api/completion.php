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
 * @copyright  2022 Bryce Yoder
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_login();

$body = json_decode(file_get_contents('php://input'), true);

if (!$body['message']) {
    http_response_code(400);
    die();
}

$apikey = get_config('block_openai_chat', 'apikey');
$prompt = get_config('block_openai_chat', 'prompt');
$agentname = get_config('block_openai_chat', 'agentname');

if (!$prompt) {
    $prompt = "Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:\n";
}
if (!$agentname) {
    $agentname = 'Agent';
}

$body['history'] .= 'User: ';

$curlbody = [
    "prompt" => $prompt . $body['history'] . $body['message'] . "\n" . $agentname . ': ',
    "temperature" => 0,
    "max_tokens" => 500,
    "top_p" => 1,
    "frequency_penalty" => 1,
    "presence_penalty" => 0,
    "stop" => "User:"
];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.openai.com/v1/engines/text-davinci-002/completions',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($curlbody),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . $apikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
