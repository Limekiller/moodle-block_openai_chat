<?php
require_once ('../../../config.php');

$body = json_decode(file_get_contents('php://input'), true);

if (!$body['message']) {
    http_response_code(400);
    die();
}

$curl = curl_init();
$api_key = get_config('block_openai_chat', 'apikey');
$prompt = get_config('block_openai_chat', 'prompt');

if (!$prompt) {
    $prompt = "Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:\n";
}
$body['history'] .= 'User: ';

$curl_body = [
    "prompt" => $prompt . $body['history'] . $body['message'] . "\n" . 'Agent: ',
    "temperature" => 0,
    "max_tokens" => 500,
    "top_p" => 1,
    "frequency_penalty" => 0.5,
    "presence_penalty" => 0,
    "stop" => "User:"
];

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.openai.com/v1/engines/text-davinci-002/completions',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($curl_body),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
