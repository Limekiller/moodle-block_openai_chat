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
 * Log table
 *
 * @package    block_openai_chat
 * @copyright  2024 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_openai_chat;
defined('MOODLE_INTERNAL') || die;

class report extends \table_sql {
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('userid', 'user_name', 'usermessage', 'airesponse', 'contextid', 'timecreated');
        $this->define_columns($columns);
        $this->no_sorting('usermessage');
        $this->no_sorting('airesponse');

        // Define the titles of columns to show in header.
        $headers = array('User ID', 'User Name', 'User Message', 'AI Response', 'Context', 'Time');
        $this->define_headers($headers);
    }

    function col_user_name($values) {
        global $DB;
        $user = $DB->get_record('user', ['id' => $values->userid]);

        if ($this->is_downloading()) {
            return "$user->firstname $user->lastname";
        } else {
            return "<a href='/user/profile.php?id=$values->userid'>$user->firstname $user->lastname</a>";
        }
    }

    function col_contextid($values) {
        $context = \context::instance_by_id($values->contextid);

        $coursecontext;
        try {
            $coursecontext = $context->get_course_context();
        } catch (\Throwable $e) {
            $coursecontext = $context;
        }
        
        if ($this->is_downloading()) {
            return '=HYPERLINK("' . $context->get_url() . '","' . $context->get_context_name() . '")';
        } else {
            return "<a href='" . $context->get_url() . "'>" . $context->get_context_name() ."</a>";
        }
    }

    function col_timecreated($values) {
        return userdate($values->timecreated);
    }
}