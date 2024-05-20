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
 * Privacy API Provider
 *
 * @package    block_openai_chat
 * @copyright  2024 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_openai_chat\privacy;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

class provider implements 
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'block_openai_chat_log',
             [
                'userid' => 'privacy:metadata:openai_chat_log:userid',
                'usermessage' => 'privacy:metadata:openai_chat_log:usermessage',
                'airesponse' => 'privacy:metadata:openai_chat_log:airesponse',
                'timecreated' => 'privacy:metadata:openai_chat_log:timecreated'
             ],
            'privacy:metadata:openai_chat_log'
        );
    
        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        $sql = "SELECT id FROM {context} WHERE contextlevel = 30 AND instanceid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid]);
        return $contextlist;
    }

    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!$context instanceof \context_user) {
            return;
        }

        if ($DB->record_exists('block_openai_chat_log', ['userid' => $context->instanceid])) {
            $userlist->add_user($context->instanceid);
        }
    }

    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $context = $contextlist->current();
        $user = $contextlist->get_user();
        $userid = $user->id;

        // Sent messages.
        $sql = "SELECT id, userid, usermessage, airesponse, timecreated FROM {block_openai_chat_log} WHERE userid = :userid";
        $records = $DB->get_records_sql($sql, ["userid" => $userid]);

        if (!empty($records)) {
            $messages = new \stdClass();
            foreach ($records as $message) {
                $messages->{$message->id} = [
                    "userid" => $message->userid,
                    "usermessage" => $message->usermessage,
                    "airesponse" => $message->airesponse,
                    "timecreated" => $message->timecreated
                ];
            }
    
            writer::with_context($context)->export_data(
                [get_string('privacy:chatmessagespath', 'block_openai_chat')],
                $messages
            );
        }
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        // Only delete data for a user context.
        if ($context->contextlevel == CONTEXT_USER) {
            $DB->delete_records('block_openai_chat_log', ['userid' => $context->instanceid]);
        }
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        foreach ($contextlist as $context) {
            // Let's be super certain that we have the right information for this user here.
            if ($context->contextlevel == CONTEXT_USER && $contextlist->get_user()->id == $context->instanceid) {
                $DB->delete_records('block_openai_chat_log', ['userid' => $context->instanceid]);
            }
        }
    }

    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if ($context instanceof \context_user && in_array($context->instanceid, $userlist->get_userids())) {
            $DB->delete_records('block_openai_chat_log', ['userid' => $context->instanceid]);
        }
    }
}
