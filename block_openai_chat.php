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
 * Block class
 *
 * @package    block_openai_chat
 * @copyright  2023 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_openai_chat extends block_base {
    public function init() {
        $this->title = get_string('openai_chat', 'block_openai_chat');
    }

    public function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        global $OUTPUT;
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        // Send data to front end
        $persistconvo = get_config('block_openai_chat', 'persistconvo');
        if (!empty($this->config)) {
            $persistconvo = (property_exists($this->config, 'persistconvo') && get_config('block_openai_chat', 'allowinstancesettings')) ? $this->config->persistconvo : $persistconvo;
        }
        $this->page->requires->js_call_amd('block_openai_chat/lib', 'init', [[
            'blockId' => $this->instance->id,
            'api_type' => get_config('block_openai_chat', 'type') ? get_config('block_openai_chat', 'type') : 'chat',
            'persistConvo' => $persistconvo
        ]]);

        // Determine if name labels should be shown.
        $showlabelscss = '';
        if (!empty($this->config) && !$this->config->showlabels) {
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
        if (!empty($this->config)) {
            $assistantname = (property_exists($this->config, 'assistantname') && $this->config->assistantname) ? $this->config->assistantname : $assistantname;
            $username = (property_exists($this->config, 'username') && $this->config->username) ? $this->config->username : $username;
        }
        $assistantname = format_string($assistantname, true, ['context' => $this->context]);
        $username = format_string($username, true, ['context' => $this->context]);

        $this->content = new stdClass;
        $this->content->text = '
            <script>
                var assistantName = "' . $assistantname . '";
                var userName = "' . $username . '";
            </script>

            <style>
                ' . $showlabelscss . '
                .openai_message.user:before {
                    content: "' . $username . '";
                }
                .openai_message.bot:before {
                    content: "' . $assistantname . '";
                }
            </style>

            <div id="openai_chat_log" role="log"></div>
        ';

        if (
            empty(get_config('block_openai_chat', 'apikey')) && 
            (!get_config('block_openai_chat', 'allowinstancesettings') || empty($this->config->apikey))
        ) {
            $this->content->footer = get_string('apikeymissing', 'block_openai_chat');
        } else {
            $contextdata = [
                'logging_enabled' => get_config('block_openai_chat', 'logging'),
                'is_edit_mode' => $PAGE->user_is_editing(),
                'pix_popout' => '/blocks/openai_chat/pix/arrow-up-right-from-square.svg',
                'pix_arrow_right' => '/blocks/openai_chat/pix/arrow-right.svg',
                'pix_refresh' => '/blocks/openai_chat/pix/refresh.svg',
            ];

            $this->content->footer = $OUTPUT->render_from_template('block_openai_chat/control_bar', $contextdata);
        }

        return $this->content;
    }
}
