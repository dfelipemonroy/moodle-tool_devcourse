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
 * Class tool_devcourse\output\entries_list.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_devcourse\output;

use renderer_base;
use moodle_url;
use tool_devcourse_table;
use context_course;

/**
 * Class tool_devcourse\output\entries_list.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entries_list implements \templatable, \renderable {

    /** @var int */
    protected $courseid;

    /**
     * @var string $pluginname The name of the plugin, used for localization and identification within Moodle.
     */
    protected $pluginname = 'tool_devcourse';

    /**
     * entries_list constructor.
     * @param int $courseid
     */
    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Implementation of exporter from templatable interface
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $course = get_course($this->courseid);
        $context = \context_course::instance($this->courseid);
        $data = [
            'courseid' => $this->courseid,
            'coursename' => format_string(
                $course->fullname,
            true,
            ['context' => $context]
            ),
        ];

        $data['enabled'] = get_config($this->pluginname, 'enabled');

        // Display table.
        ob_start();
        $table = new \tool_devcourse\table($this->pluginname, $this->courseid);
        $table->out(20, false);
        $data['contents'] = ob_get_clean();

        // Link to add new entry.
        if (has_capability('tool/devcourse:edit', $context)) {
            $url = new moodle_url('/admin/tool/devcourse/edit.php', ['courseid' => $this->courseid]);
            $data['addlink'] = $url->out(false);
        }

        return $data;
    }
}
