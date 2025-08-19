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

require_once(__DIR__ . '/../../../config.php');

$courseid = required_param('id', PARAM_INT);

$url = new moodle_url('/admin/tool/devcourse/index.php', ['id' => $courseid]);
$pluginname = 'tool_devcourse';

require_login($courseid);
$context = context_course::instance($courseid);
require_capability('tool/devcourse:view', $context);

$PAGE->set_url($url);
$PAGE->set_title(get_string('helloworld', $pluginname));
$PAGE->set_heading(get_string('pluginname', $pluginname));

// Delete entry if requested.
if ($deleteid = optional_param('delete', null, PARAM_INT)) {
    require_sesskey();
    $record = tool_devcourse_api::retrieve($deleteid, $courseid);
    require_capability('tool/devcourse:edit', $PAGE->context);
    tool_devcourse_api::delete($record->id);

    redirect(new moodle_url('/admin/tool/devcourse/index.php', ['id' => $courseid]));
}

$outputpage = new \tool_devcourse\output\entries_list($courseid);
$output = $PAGE->get_renderer('tool_devcourse');

echo $output->header();
echo $output->render($outputpage);
echo $output->footer();
