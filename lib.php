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

function get_models() {
    return [
        "models" => [
            'gpt-4' => 'gpt-4',
            'gpt-4-0314' => 'gpt-4-0314',
            'gpt-4-0613' => 'gpt-4-0613',
            'gpt-3.5-turbo' => 'gpt-3.5-turbo',
            'gpt-3.5-turbo-16k-0613' => 'gpt-3.5-turbo-16k-0613',
            'gpt-3.5-turbo-16k' => 'gpt-3.5-turbo-16k',
            'gpt-3.5-turbo-0613' => 'gpt-3.5-turbo-0613',
            'gpt-3.5-turbo-0301' => 'gpt-3.5-turbo-0301',
            'text-davinci-003' => 'text-davinci-003',
            'text-davinci-002' => 'text-davinci-002',
            'text-davinci-001' => 'text-davinci-001',
            'text-curie-001' => 'text-curie-001',
            'text-babbage-001' => 'text-babbage-001',
            'text-ada-001' => 'text-ada-001',
            'davinci' => 'davinci',
            'curie' => 'curie',
            'babbage' => 'babbage',
            'ada' => 'ada'
        ],
        "types" => [
            'gpt-4' => 'chat',
            'gpt-4-0314' => 'chat',
            'gpt-4-0613' => 'chat',
            'gpt-3.5-turbo' => 'chat',
            'gpt-3.5-turbo-16k-0613' => 'chat',
            'gpt-3.5-turbo-16k' => 'chat',
            'gpt-3.5-turbo-0613' => 'chat',
            'gpt-3.5-turbo-0301' => 'chat',
            'text-davinci-003' => 'basic',
            'text-davinci-002' => 'basic',
            'text-davinci-001' => 'basic',
            'text-curie-001' => 'basic',
            'text-babbage-001' => 'basic',
            'text-ada-001' => 'basic',
            'davinci' => 'basic',
            'curie' => 'basic',
            'babbage' => 'basic',
            'ada' => 'basic'
        ]
    ];
}