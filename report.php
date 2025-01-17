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
 * Log report table
 *
 * @package    block_openai_chat
 * @copyright  2024 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \block_openai_chat\report;

require_once('../../config.php');
require_once($CFG->libdir.'/tablelib.php');
global $DB;

$courseid = required_param('courseid', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$user = optional_param('user', '', PARAM_TEXT);
$starttime = optional_param('starttime', '', PARAM_TEXT);
$endtime = optional_param('endtime', '', PARAM_TEXT);
$tsort = optional_param('tsort', '', PARAM_TEXT);

$pageurl = $CFG->wwwroot . "/blocks/openai_chat/report.php?courseid=$courseid" .
    "&user=$user" .
    "&starttime=$starttime" .
    "&endtime=$endtime";
$starttime_ts = strtotime($starttime);
$endtime_ts = strtotime($endtime);
$course = $DB->get_record('course', ['id' => $courseid]);

$PAGE->set_url($pageurl);
require_login($course);
$context = context_course::instance($courseid);
require_capability('block/openai_chat:viewreport', $context);

$datetime = new DateTime();
$table = new \block_openai_chat\report(time());
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->is_downloading(
    $download, 
    get_string('downloadfilename', 'block_openai_chat') 
        . '_' 
        . $datetime->format(DateTime::ATOM)
);

if (!$table->is_downloading()) {
    $PAGE->set_pagelayout('report');
    $PAGE->set_title(get_string('openai_chat_logs', 'block_openai_chat'));
    $PAGE->set_heading(get_string('openai_chat_logs', 'block_openai_chat'));
    $PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', ['id' => $course->id]));
    $PAGE->navbar->add(get_string('openai_chat_logs', 'block_openai_chat'), new moodle_url($pageurl));

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('block_openai_chat/report_page', [
        "courseid" => $courseid,
        "user" => $user,
        "starttime" => $starttime,
        "endtime" => $endtime,
        "link" => (new moodle_url("/blocks/openai_chat/report.php"))->out()
    ]);
}

$where = "1=1";
$out = 10;

// If courseid is 1, we're assuming this is an admin report wanting the entire log table
// otherwise, we'll limit it to responses in the course context for this course
if ($courseid !== 1) {
    $where = "c.contextlevel = 50 AND co.id = $courseid";
}

// filter by user, starttime, endtime
if ($user) {
    $where .= " AND CONCAT(u.firstname, ' ', u.lastname) like '%$user%'";
}
if ($starttime_ts) {
    $where .= " AND ocl.timecreated > $starttime_ts";
}
if ($endtime_ts) {
    $where .= " AND ocl.timecreated < $endtime_ts";
}

if (!$tsort) {
    $where .= " ORDER BY ocl.timecreated DESC";
}

$table->set_sql(
    "ocl.*, CONCAT(u.firstname, ' ', u.lastname) as user_name", 
    "{block_openai_chat_log} ocl 
        JOIN {user} u ON u.id = ocl.userid 
        JOIN {context} c ON c.id = ocl.contextid
        LEFT JOIN {course} co ON co.id = c.instanceid",
    $where
);
$table->define_baseurl($pageurl);
$table->out($out, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}