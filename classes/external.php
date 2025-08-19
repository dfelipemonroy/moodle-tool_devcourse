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

namespace tool_devcourse;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_api;
use core_external\external_value;

/**
 * External API class for the devcourse tool.
 *
 * This class extends the core external_api and provides external functions
 * for the devcourse admin tool in Moodle Workplace.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns the parameters required for the delete_entry external function.
     *
     * @return external_function_parameters The parameters definition for the function.
     */
    public static function delete_entry_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Entry ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Deletes an entry with the specified ID.
     *
     * @param int $id The ID of the entry to delete.
     * @return array An array containing the status of the deletion.
     */
    public static function delete_entry(int $id) {
        // Validate parameters.
        $params = self::validate_parameters(self::delete_entry_parameters(), ['id' => $id]);

        // We retrieve the entry to be deleted.
        $entry = \tool_devcourse_api::retrieve($params['id']);

        // Permission check.
        $context = \context_course::instance($entry->courseid);
        self::validate_context($context);
        require_capability('tool/devcourse:edit', $context);

        \tool_devcourse_api::delete($params['id']);

        return ['status' => 'OK'];
    }

    /**
     * Returns description of method return value for delete_entry.
     *
     * @return external_single_structure
     */
    public static function delete_entry_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status message')
        ]);
    }

    /**
     * Returns the parameters required for the list_entries external function.
     *
     * @return external_function_parameters The parameters definition for the external function.
     */
    public static function list_entries_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Retrieves a list of entries for the specified course.
     *
     * @param int $courseid The ID of the course to list entries for.
     * @return array The list of entries associated with the course.
     */
    public static function list_entries(int $courseid): array {
        global $PAGE;

        // Validate parameters.
        $params = self::validate_parameters(self::list_entries_parameters(), ['courseid' => $courseid]);

        // Permission check.
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('tool/devcourse:view', $context);

        $output = new \tool_devcourse\output\entries_list($params['courseid']);
        $renderer = $PAGE->get_renderer('tool_devcourse');

        return $output->export_for_template($renderer);
    }

    /**
     * Returns the structure of the data returned by the list_entries external function.
     *
     * @return external_single_structure The structure describing the returned data.
     */
    public static function list_entries_returns(): external_single_structure {
        return new external_single_structure([
            'courseid' => new external_value(PARAM_INT, 'Course id'),
            'coursename' => new external_value(PARAM_NOTAGS, 'Course name'),
            'contents' => new external_value(PARAM_RAW, 'Entries table contents'),
            'addlink' => new external_value(PARAM_URL, 'Link to the entry edition form', VALUE_OPTIONAL),
        ]);
    }

}
