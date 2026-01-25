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

    private function get_config_item($name, $defaultvalue) {
        $configitem = get_config('block_openai_chat', $name) ?: $defaultvalue;
        if (!empty($this->config) && get_config('block_openai_chat', 'allowinstancesettings')) {
            if (property_exists($this->config, $name) && $this->config->$name !== NULL && $this->config->$name !== "") {
                $configitem = $this->config->$name;
            }
        }
        return $configitem;
    }

    public function get_content() {
        global $OUTPUT;
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $assistantname = $this->get_config_item('assistantname', get_string('defaultassistantname', 'block_openai_chat'));
        $assistantname = format_string($assistantname, true, ['context' => $this->context]);
        $username = $this->get_config_item('username', get_string('defaultusername', 'block_openai_chat'));
        $username = format_string($username, true, ['context' => $this->context]);

        $persistconvo = (int) $this->get_config_item('persistconvo', false);

        $this->page->requires->js_call_amd('block_openai_chat/lib', 'init', [[
            'blockId' => $this->instance->id,
            'persistConvo' => $persistconvo,
            'userName' => $username,
            'assistantName' => $assistantname
        ]]);

        $contextdata = [
            'logging_enabled' => get_config('block_openai_chat', 'logging'),
            'is_edit_mode' => $PAGE->user_is_editing(),
            'user_name' => $username,
            'assistant_name' => $assistantname,
            'show_labels' => !empty($this->config) && !$this->config->showlabels
        ];
        $this->content = new stdClass;
        $this->content->text = '<div id="openai_chat_log" role="log"></div>';
        $this->content->footer = $OUTPUT->render_from_template('block_openai_chat/control_bar', $contextdata);

        return $this->content;
    }
}
