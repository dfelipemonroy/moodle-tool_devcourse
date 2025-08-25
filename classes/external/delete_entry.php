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
use core_external\external_api;
use core_external\external_value;

/**
 * External API class delete_entry for the devcourse tool.
 *
 * This class extends the core external_api and provides external functions
 * for the devcourse admin tool in Moodle Workplace.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_entry extends external_api {

    /**
     * Returns the parameters required for the execute external function.
     *
     * @return external_function_parameters The parameters definition for the function.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Entry ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Deletes an entry with the specified ID.
     *
     * @param int $id The ID of the entry to delete.
     */
    public static function execute(int $id) {
        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), ['id' => $id]);

        // We retrieve the entry to be deleted.
        $entry = \tool_devcourse\api::retrieve($params['id']);

        // Permission check.
        $context = \context_course::instance($entry->courseid);
        self::validate_context($context);
        require_capability('tool/devcourse:edit', $context);

        // Delete the entry.
        \tool_devcourse\api::delete($params['id']);
    }

    /**
     * Returns the structure of the data returned by the execute function.
     *
     * @return null No data is needed to be returned.
     */
    public static function execute_returns() {
        return null;
    }

}
