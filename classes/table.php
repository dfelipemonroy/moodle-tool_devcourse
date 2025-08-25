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
 * Class tool_devcourse_table.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_devcourse;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/tablelib.php');

/**
 * Table class for the Dev Course tool.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table extends \table_sql {

    /** @var context_course */
    protected $context;

    /**
     * The name of the plugin associated with this class.
     *
     * @var string
     */
    protected $pluginname = 'tool_devcourse';

    /**
     * Constructor for the table class.
     *
     * @param string $uniqueid A unique identifier for the table instance.
     * @param int $courseid The ID of the course associated with this table.
     *
     * @return void
     */
    public function __construct($uniqueid, $courseid) {
        global $PAGE;

        parent::__construct($uniqueid);

        $this->set_attribute('id', 'tool_devcourse_overview');

        // Set the table's unique identifier and course ID.
        $columns = ['name', 'description', 'completed', 'priority', 'timecreated', 'timemodified'];
        $headers = [
            get_string('name', $this->pluginname),
            get_string('description', $this->pluginname),
            get_string('completed', $this->pluginname),
            get_string('priority', $this->pluginname),
            get_string('timecreated', $this->pluginname),
            get_string('timemodified', $this->pluginname),
        ];

        // Set the context for the table.
        $this->context = \context_course::instance($courseid);
        if (has_capability('tool/devcourse:edit', $this->context)) {
            $columns[] = 'edit';
            $headers[] = '';
        }

        // Define the columns and headers for the table.
        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->pageable(true);
        $this->collapsible(false);
        $this->sortable(false);
        $this->is_downloadable(false);
        $this->define_baseurl($PAGE->url);

        // Set the SQL query for the table.
        $fields = 'id, name, description, descriptionformat, completed, priority, timecreated, timemodified';
        $this->set_sql(
            $fields,
            '{tool_devcourse}',
            'courseid = ?',
            [$courseid]
        );
    }

    /**
     * Returns the formatted value for the 'completed' column in the table.
     *
     * @param object $row The data object representing the current row.
     *
     * @return string The formatted output for the 'completed' column.
     */
    protected function col_completed($row) {
        return $row->completed ? get_string('yes') : get_string('no');
    }

    /**
     * Returns the formatted value for the 'priority' column in the table.
     *
     * @param object $row The data object representing the current row.
     *
     * @return string The formatted output for the 'priority' column.
     */
    protected function col_priority($row) {
        return $row->priority ? get_string('yes') : get_string('no');
    }

    /**
     * Displays column name.
     *
     * @param stdClass $row
     *
     * @return string
     */
    protected function col_name($row) {
        return format_string($row->name, true,
            ['context' => $this->context]);
    }

    /**
     * Returns the formatted description column for the given row.
     *
     * @param object $row The data object representing a row in the table.
     *
     * @return string The HTML-formatted description for display in the table.
     */
    protected function col_description($row) {
        global $PAGE;

        $options = api::editor_options();
        $description = file_rewrite_pluginfile_urls(
            $row->description,
            'pluginfile.php',
            $PAGE->context->id,
            $this->pluginname,
            'entry',
            $row->id
        );
        return format_text($description, $row->descriptionformat, $options);
    }

    /**
     * Returns the formatted value for the 'timecreated' column in the table.
     *
     * @param object $row The data object representing the current row.
     *
     * @return string The formatted output for the 'timecreated' column.
     */
    protected function col_timecreated($row) {
        return userdate($row->timecreated, get_string('strftimedatetime'));
    }

    /**
     * Displays column timemodified.
     *
     * @param object $row The data object representing the current row.
     *
     * @return string The formatted output for the 'timemodified' column.
     */
    protected function col_timemodified($row) {
        return userdate($row->timemodified, get_string('strftimedatetime'));
    }

    /**
     * Generates the content for the 'edit' column in the table.
     *
     * @param object $row The data object representing the current row in the table.
     * @return string The HTML content to display in the 'edit' column.
     */
    protected function col_edit($row) {
        $output = '';
        $editurl = new \moodle_url('/admin/tool/devcourse/edit.php', ['id' => $row->id]);
        $deleteurl = new \moodle_url('/admin/tool/devcourse/index.php', [
            'delete' => $row->id,
            'id' => $this->context->instanceid,
            'sesskey' => sesskey(),
        ]);

        $output .= \html_writer::link(
            $editurl,
            get_string('edit') . '<br>'
        );
        $output .= \html_writer::link(
            $deleteurl,
            get_string('delete'),
            [
                'data-action' => 'deleteentry',
                'data-entryid' => $row->id,
            ]
        );

        return $output;
    }
}
