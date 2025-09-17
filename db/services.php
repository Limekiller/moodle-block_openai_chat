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
 * File description.
 *
 * @package   block_openai_chat
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(

    'block_openai_chat_answer' => array(
        'classpath'   => 'blocks/openai_chat/classes/external/answer.php',
        'classname'   => 'block_openai_chat\external\answer',
        'methodname'  => 'execute',
        'description' => 'Get answer.',
        'type'        => 'read',
        'ajax'        => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);
