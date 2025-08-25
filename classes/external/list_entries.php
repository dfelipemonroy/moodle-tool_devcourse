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

namespace tool_devcourse\external;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_api;
use core_external\external_value;

/**
 * External API class list_entries for the devcourse tool.
 *
 * This class extends the core external_api and provides external functions
 * for the devcourse admin tool in Moodle Workplace.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_entries extends external_api {

    /**
     * Returns the parameters required for the execute external function.
     *
     * @return external_function_parameters The parameters definition for the external function.
     */
    public static function execute_parameters(): external_function_parameters {
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
    public static function execute(int $courseid): array {
        global $PAGE;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);

        // Permission check.
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('tool/devcourse:view', $context);

        $output = new \tool_devcourse\output\entries_list($params['courseid']);
        $renderer = $PAGE->get_renderer('tool_devcourse');

        return $output->export_for_template($renderer);
    }

    /**
     * Returns the structure of the data returned by the execute external function.
     *
     * @return external_single_structure The structure describing the returned data.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'courseid' => new external_value(PARAM_INT, 'Course id'),
            'coursename' => new external_value(PARAM_NOTAGS, 'Course name'),
            'contents' => new external_value(PARAM_RAW, 'Entries table contents'),
            'addlink' => new external_value(PARAM_URL, 'Link to the entry edition form', VALUE_OPTIONAL),
        ]);
    }

}
