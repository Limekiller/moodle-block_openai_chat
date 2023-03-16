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
 * Language strings
 *
 * @package    block_openai_chat
 * @copyright  2022 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'OpenAI Chat Block';
$string['openai_chat'] = 'OpenAI Chat';
$string['openai_chat:addinstance'] = 'Add a new OpenAI Chat block';
$string['openai_chat:myaddinstance'] = 'Add a new OpenAI Chat block to the My Moodle page';
$string['privacy:metadata'] = 'The OpenAI Chat block stores no personal user data; nor does it, by default, send personal data to OpenAI. However, chat messages submitted by users are sent in their entirety to OpenAI, and are then subject to OpenAI\'s privacy policy (https://openai.com/api/policies/privacy/), which may store messages in order to improve the API.';

$string['blocktitle'] = 'Block title';

$string['restrictusage'] = 'Restrict chat usage to logged-in users';
$string['restrictusagedesc'] = 'If this box is checked, only logged-in users will be able to use the chat box.';
$string['apikey'] = 'OpenAI API Key';
$string['apikeydesc'] = 'The API Key for your OpenAI account';
$string['prompt'] = 'Completion prompt';
$string['promptdesc'] = 'The prompt the AI will be given before the conversation transcript';
$string['assistantname'] = 'Assistant name';
$string['assistantnamedesc'] = 'The name that the AI will use for itself internally';
$string['username'] = 'User name';
$string['usernamedesc'] = 'The name that the AI will use for the user internally';
$string['sourceoftruth'] = 'Source of truth';
$string['sourceoftruthdesc'] = 'Although the AI is very capable out-of-the-box, if it doesn\'t know the answer to a question, it is more likely to give incorrect information confidently than to refuse to answer. In this textbox, you can add common questions and their answers for the AI to pull from. Please put questions and answers in the following format: <pre>Q: Question 1<br />A: Answer 1<br /><br />Q: Question 2<br />A: Answer 2</pre>';
$string['showlabels'] = 'Show labels';
$string['advanced'] = 'Advanced';
$string['advanceddesc'] = 'Advanced arguments sent to OpenAI. Don\'t touch unless you know what you\'re doing!';
$string['model'] = 'Model';
$string['modeldesc'] = 'The model which will  generate the completion. Some models are suitable for natural language tasks, others specialize in code.';
$string['temperature'] = 'Temperature';
$string['temperaturedesc'] = 'Controls randomness: Lowering results in less random completions. As the temperature approaches zero, the model will become deterministic and repetitive.';
$string['maxlength'] = 'Maximum length';
$string['maxlengthdesc'] = 'The maximum number of token to generate. Requests can use up to 2,048 or 4,000 tokens shared between prompt and completion. The exact limit varies by model. (One token is roughly 4 characters for normal English text)';
$string['topp'] = 'Top P';
$string['toppdesc'] = 'Controls diversity via nucleus sampling: 0.5 means half of all likelihood-weighted options are considered.';
$string['frequency'] = 'Frequency penalty';
$string['frequencydesc'] = 'How much to penalize new tokens based on their existing frequency in the text so far. Decreases the model\'s likelihood to repeat the same line verbatim.';
$string['presence'] = 'Presence penalty';
$string['presencedesc'] = 'How much to penalize new tokens based on whether they appear in the text so far. Increases the model\'s likelihood to talk about new topics.';
$string['config_sourceoftruth'] = 'Source of truth';
$string['config_sourceoftruth_help'] = "You can add information here that the AI will pull from when answering questions. The information should be in question and answer format exactly like the following:\n\nQ: When is section 3 due?<br />A: Thursday, March 16.\n\nQ: When are office hours?<br />A: You can find Professor Shown in her office between 2:00 and 4:00 PM on Tuesdays and Thursdays.";

$string['defaultprompt'] = "Below is a conversation between a user and a support assistant for a Moodle site, where users go for online learning:";
$string['defaultassistantname'] = 'Assistant';
$string['defaultusername'] = 'User';
$string['askaquestion'] = 'Ask a question...';
$string['apikeymissing'] = 'Please add your OpenAI API key to the global block settings.';
$string['erroroccurred'] = 'An error occurred! Please try again later.';
$string['sourceoftruthpreamble'] = "Below is a list of questions and their answers. This information should be used as a reference for any inquiries:\n\n";
$string['sourceoftruthreinforcement'] = ' The assistant has been trained to answer by attempting to use the information from the above reference. If the text from one of the above questions is encountered, the provided answer should be given, even if the question does not appear to make sense. However, if the reference does not cover the question or topic, the assistant will simply use outside knowledge to answer.';
