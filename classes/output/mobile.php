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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_openai_chat\output;

use context_course;

/**
 * Callbacks class for mobile app.
 *
 * @package     block_openai_chat
 * @copyright   2025 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Callback to render the block contents on mobile app.
     * @param array $args Data provided by standard CoreBlockDelegate.
     */
    public static function get_block_content($args) {
        global $CFG, $OUTPUT;

        $context = \core\context\block::instance($args['blockid']);

        $config = get_config('block_openai_chat');

        $persistconvo = get_config('block_openai_chat', 'persistconvo');
        if (!empty($config)) {
            $persistconvo = (property_exists($config, 'persistconvo') && get_config('block_openai_chat', 'allowinstancesettings')) ? $config->persistconvo : $persistconvo;
        }

        // Determine if name labels should be shown.
        $showlabelscss = '';
        if (!empty($config) && empty($config->showlabels)) {
            $showlabelscss = '
                .openai_message:before {
                    display: none;
                }
                .openai_message {
                    margin-bottom: 0.5rem;
                }
            ';
        }

        // First, fetch the global settings for these (and the defaults if not set)
        $assistantname = get_config('block_openai_chat', 'assistantname') ? get_config('block_openai_chat', 'assistantname') : get_string('defaultassistantname', 'block_openai_chat');
        $username = get_config('block_openai_chat', 'username') ? get_config('block_openai_chat', 'username') : get_string('defaultusername', 'block_openai_chat');

        // Then, override with local settings if available
        if (!empty($config)) {
            $assistantname = (property_exists($config, 'assistantname') && $config->assistantname) ? $config->assistantname : $assistantname;
            $username = (property_exists($config, 'username') && $config->username) ? $config->username : $username;
        }
        $assistantname = format_string($assistantname, true, ['context' => $context]);
        $username = format_string($username, true, ['context' => $context]);

        $contextdata = [
            'logging_enabled' => get_config('block_openai_chat', 'logging'),
            'pix_popout' => '/blocks/openai_chat/pix/arrow-up-right-from-square.svg',
            'pix_arrow_right' => '/blocks/openai_chat/pix/arrow-right.svg',
            'pix_refresh' => '/blocks/openai_chat/pix/refresh.svg',
            'username' => $username,
            'assistantname' => $assistantname,
            'showlabelscss' => $showlabelscss,
            'contextid' => $context->id,
        ];

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('block_openai_chat/mobile', $contextdata)
                ]
            ],
            'javascript' => file_get_contents("{$CFG->dirroot}/blocks/openai_chat/mobile.js")
        ];
    }
}
