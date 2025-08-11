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
 * Class tool_devcourse_api.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class tool_devcourse_api for various api methods.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_devcourse_api {

    /**
     * The name of the database table used by this class.
     *
     * @var string
     */
    protected static $table = 'tool_devcourse';

    /**
     * Retrieve an entry
     *
     * @param int $id id of the entry
     * @param int $courseid optional course id for validation
     * @param int $strictness
     *
     * @return stdClass|bool retrieved object or false if entry not found and strictness is IGNORE_MISSING
     */
    public static function retrieve(int $id, int $courseid = 0, int $strictness = MUST_EXIST) {
        global $DB;
        $params = ['id' => $id];
        if ($courseid) {
            $params['courseid'] = $courseid;
        }
        return $DB->get_record(self::$table, $params, '*', $strictness);
    }

    /**
     * Update an entry.
     *
     * @param stdClass $data
     */
    public static function update(stdClass $data) {
        global $DB;
        if (empty($data->id)) {
            throw new coding_exception('Object data must contain property id');
        }
        // Only fields name, completed, priority can be modified.
        $updatedata = array_intersect_key((array)$data,
            ['id' => 1, 'name' => 1, 'completed' => 1, 'priority' => 1]);
        $updatedata['timemodified'] = time();
        $DB->update_record(self::$table, $updatedata);
    }

    /**
     * Insert an entry.
     *
     * @param stdClass $data
     *
     * @return int id of the new entry
     */
    public static function insert(stdClass $data) : int {
        global $DB;
        if (empty($data->courseid)) {
            throw new coding_exception('Object data must contain property courseid');
        }
        $insertdata = array_intersect_key((array)$data,
            ['courseid' => 1, 'name' => 1, 'completed' => 1, 'priority' => 1]);
        $insertdata['timemodified'] = $insertdata['timecreated'] = time();
        return $DB->insert_record(self::$table, $insertdata);
    }

    /**
     * Delete an entry.
     *
     * @param int $id
     */
    public static function delete(int $id) {
        global $DB;
        $DB->delete_records(self::$table, ['id' => $id]);
    }
}
