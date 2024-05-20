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
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('openai_chat_report');
$pageurl = $CFG->wwwroot . '/blocks/openai_chat/report.php';

$download = optional_param('download', '', PARAM_ALPHA);
$user = optional_param('user', '', PARAM_TEXT);
$starttime = optional_param('starttime', '', PARAM_TEXT);
$endtime = optional_param('endtime', '', PARAM_TEXT);

$starttime_ts = strtotime($starttime);
$endtime_ts = strtotime($endtime);

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
    $PAGE->set_url($pageurl);
    $PAGE->set_title(get_string('openai_chat_logs', 'block_openai_chat'));
    $PAGE->set_heading(get_string('openai_chat_logs', 'block_openai_chat'));
    $PAGE->navbar->add(get_string('openai_chat_logs', 'block_openai_chat'), new moodle_url($pageurl));
    echo $OUTPUT->header();

    echo "<form class='block_openai_chat' method='GET' action='" . (new moodle_url('/blocks/openai_chat/report.php'))->out() . "'>
        <div class='report_container'>
            <div style='display: flex; flex-direction: column'>
                <label for='user'>Search by user name</label>
                <input name='user' type='text' value='$user' placeholder='User name'/>
            </div>
            <div style='display: flex; flex-direction: column'>
                <label for='starttime'>Start time</label>
                <input type='datetime-local' name='starttime' value='$starttime' />
            </div>
            <div style='display: flex; flex-direction: column'>
                <label for='endtime'>End time</label>
                <input type='datetime-local' name='endtime' value='$endtime' />
            </div>
        </div>
        <button class='btn btn-primary' type='submit'>Search</button>
    </form>";
}

$where = "1=1";
$out = 10;
if ($user) {
    $out = -1;
    $where = "CONCAT(u.firstname, ' ', u.lastname) like '%$user%'";
}

if ($starttime_ts) {
    $out = -1;
    $where .= " AND ocl.timecreated > $starttime_ts";
}

if ($endtime_ts) {
    $out = -1;
    $where .= " AND ocl.timecreated < $endtime_ts";
}

$table->set_sql(
    "ocl.*, CONCAT(u.firstname, ' ', u.lastname) as user_name", 
    "{block_openai_chat_log} ocl JOIN {user} u ON u.id = ocl.userid", 
    $where
);
$table->define_baseurl($pageurl);
$table->out($out, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}