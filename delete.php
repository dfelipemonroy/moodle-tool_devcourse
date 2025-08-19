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
 * Deletes an existing entry permanently.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');

// Get the entry ID.
$id = required_param('id', PARAM_INT);
require_sesskey();

// Retrieve the entry.
$entry = \tool_devcourse_api::retrieve($id, 0, MUST_EXIST);
$courseid = $entry->courseid;
$params = ['id' => $id];
$contextcourse = \context_course::instance($courseid);

// Check permissions.
require_login($courseid);
require_capability('tool/devcourse:edit', $contextcourse);

$url = new moodle_url('/admin/tool/devcourse/delete.php', $params);
$returnurl = new moodle_url('/admin/tool/devcourse/index.php', ['id' => $courseid]);
$PAGE->set_url($url);
$PAGE->set_context($contextcourse);

// Delete the entry.
\tool_devcourse_api::delete($id);
redirect($returnurl);
