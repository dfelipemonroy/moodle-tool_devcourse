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
 * Example page for the Dev Course tool.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use moodle_url;
use context_system;
use html_writer;

require_once(__DIR__ . '/../../../config.php');

$courseid = required_param('id', PARAM_INT);

$url = new moodle_url('/admin/tool/devcourse/index.php', ['id' => $courseid]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('helloworld', 'tool_devcourse'));
$PAGE->set_heading(get_string('pluginname', 'tool_devcourse'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('helloworld', 'tool_devcourse'));
echo html_writer::div(get_string('youareviewing', 'tool_devcourse', $courseid));
$course = $DB->get_record_sql("SELECT shortname, fullname FROM {course} WHERE id = ?", [$courseid]);
echo html_writer::div(format_string($course->fullname));
echo $OUTPUT->footer();
