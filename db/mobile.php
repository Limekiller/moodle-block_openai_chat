<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Mobile App addons definition.
 *
 * @package     block_openai_chat
 * @copyright   2025 Daniel Neis Araujo
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

$addons = [
    'block_openai_chat' => [
        'handlers' => [
            'completionlevels' => [
                'delegate' => 'CoreBlockDelegate',
                'method' => 'get_block_content',
                'displaydata' => [
                    'class' => 'block block_openai_chat',
                ],
                'styles' => [
                    'url' => $CFG->wwwroot . '/blocks/openai_chat/styles.css',
                    'version' => 11,
                ],
            ],
        ],
        'lang' => [
                [ 'pluginname', 'block_openai_chat' ],
        ],
    ],
];
