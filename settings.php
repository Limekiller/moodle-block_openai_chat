<?php
$settings->add(new admin_setting_configtext(
    'block_openai_chat/apikey',
    get_string('apikey', 'block_openai_chat'),
    get_string('apikeydesc', 'block_openai_chat'),
    '',
    PARAM_TEXT
));

$settings->add(new admin_setting_configtextarea(
    'block_openai_chat/prompt',
    get_string('prompt', 'block_openai_chat'),
    get_string('promptdesc', 'block_openai_chat'),
    "Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:\nUser: ",
    PARAM_TEXT
));