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
     * The name of the plugin.
     *
     * @var string
     */
    protected static $pluginname = 'tool_devcourse';

    /**
     * Retrieve an entry
     *
     * @param int $id id of the entry
     * @param int $courseid optional course id for validation
     * @param int $strictness
     *
     * @return stdClass|null
     */
    public static function retrieve(int $id, int $courseid = 0, int $strictness = MUST_EXIST) {
        global $DB;

        $cache = cache::make(self::$pluginname, 'entry');
        $entry = null;
        if (!$entry = $cache->get($id)) {
            $params = ['id' => $id];
            if ($courseid) {
                $params['courseid'] = $courseid;
            }

            $entry = $DB->get_record(self::$table, $params, '*', $strictness);
            if (!empty($entry) && is_object($entry)) {
                $cache->set($entry->id, $entry);
            }
        }

        return $entry;
    }

    /**
     * Update an entry.
     *
     * @param stdClass $data
     */
    public static function update(stdClass $data) {
        global $DB, $PAGE;

        if (empty($data->id)) {
            throw new \coding_exception('Object data must contain property id');
        }

        if (isset($data->description_editor)) {
            $data = file_postupdate_standard_editor($data, 'description',
                self::editor_options(), $PAGE->context, self::$pluginname, 'entry', $data->id);
        }
        $updatedata = array_intersect_key((array) $data, [
            'id' => 1,
            'name' => 1,
            'completed' => 1,
            'priority' => 1,
            'description' => 1,
            'descriptionformat' => 1,
        ]);
        $updatedata['timemodified'] = time();
        $DB->update_record(self::$table, $updatedata);

        $cache = cache::make(self::$pluginname, 'entry');
        $cache->set($data->id, $data);

        // We need to trigger an event for the updated entry.
        $entry = self::retrieve($data->id);
        $event = \tool_devcourse\event\entry_updated::create([
            'context' => context_course::instance($entry->courseid),
            'objectid' => $entry->id,
        ]);
        $event->trigger();
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
            throw new \coding_exception('Object data must contain property courseid');
        }

        $context = context_course::instance($data->courseid);

        $insertdata = array_intersect_key((array) $data, [
            'courseid' => 1,
            'name' => 1,
            'completed' => 1,
            'priority' => 1,
            'description' => 1,
            'descriptionformat' => 1,
        ]);
        $now = time();
        $insertdata = array_intersect_key((array) $data, [
            'courseid' => 1,
            'name' => 1,
            'completed' => 1,
            'priority' => 1,
            'description' => 1,
            'descriptionformat' => 1,
        ]);
        $insertdata['timecreated'] = $now;
        $insertdata['timemodified'] = $now;
        $entryid = $DB->insert_record(self::$table, $insertdata);

        if (!empty($data->description_editor)) {
            $data = file_postupdate_standard_editor($data, 'description',
                self::editor_options(), $context, self::$pluginname, 'entry', $entryid);
            $updatedata = [
                'id' => $entryid,
                'description' => $data->description,
                'descriptionformat' => $data->descriptionformat,
            ];
            $DB->update_record(self::$table, $updatedata);
        }

        // We need to trigger an event for the newly created entry.
        $event = \tool_devcourse\event\entry_created::create([
            'context' => $context,
            'objectid' => $entryid,
        ]);
        $event->trigger();

        return $entryid;
    }

    /**
     * Delete an entry.
     *
     * @param int $id id of the entry to delete.
     */
    public static function delete(int $id) {
        global $DB;
        $entry = self::retrieve($id, 0, IGNORE_MISSING);
        if (empty($entry) || !is_object($entry)) {
            return;
        }
        $DB->delete_records(self::$table, ['id' => $id]);

        $cache = cache::make(self::$pluginname, 'entry');
        $cache->delete($id);

        // We need to trigger an event for the deleted entry.
        $event = \tool_devcourse\event\entry_deleted::create([
            'context' => context_course::instance($entry->courseid),
            'objectid' => $entry->id,
        ]);
        $event->trigger();
    }

    /**
     * Returns the configuration options for the editor.
     *
     * This static method provides an array of options that can be used to configure
     * the editor instance within the application. The returned options may include
     * settings such as toolbar configuration, plugins, and other editor preferences.
     *
     * @return array The array of editor configuration options.
     */
    public static function editor_options() {
        global $PAGE;

        return [
            'context' => $PAGE->context,
            'noclean' => true,
        ];
    }

    /**
     * Observer for the course_deleted event.
     *
     * This method is triggered when a course is deleted in Moodle.
     * It handles any necessary cleanup or processing related to the course deletion event.
     *
     * @param \core\event\course_deleted $event The event object containing details about the deleted course.
     */
    public static function course_deleted_observer(\core\event\course_deleted $event) {
        global $DB;

        $DB->delete_records('tool_devcourse', ['courseid' => $event->objectid]);
    }

}
