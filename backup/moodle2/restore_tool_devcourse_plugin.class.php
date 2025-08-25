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
 * Restore support for tool_devcourse plugin.
 *
 * @package    tool_devcourse
 * @category   backup
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\oauth2\rest;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/moodle2/restore_tool_plugin.class.php');

/**
 * Class restore_tool_devcourse_plugin.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_tool_devcourse_plugin extends restore_tool_plugin {

    /**
     * Defines the structure for restoring the course plugin.
     *
     * This method specifies the XML paths and data structure required to restore
     * the plugin's data during the course restore process.
     *
     * @return restore_path_element[] An array of restore_path_element objects.
     */
    protected function define_course_plugin_structure() {
        $paths = [
            new restore_path_element('entry', $this->get_pathfor('/entry')),
        ];
        return $paths;
    }

    /**
     * Processes a single entry during the restore process.
     *
     * This method is called for each entry found in the backup data. It handles
     * the restoration of the entry's data into the current course or context.
     *
     * @param stdClass $data The data object representing the entry to be processed.
     * @return void
     */
    public function process_entry($data) {
        $data           = (object) $data;
        $courseid       = $this->task->get_courseid();
        $data->courseid = $courseid;

        \tool_devcourse\api::insert((object) $data);
    }

}
