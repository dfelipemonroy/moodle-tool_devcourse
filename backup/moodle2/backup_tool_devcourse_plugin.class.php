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
 * Backup support for tool_devcourse plugin.
 *
 * @package    tool_devcourse
 * @category   backup
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/moodle2/backup_tool_plugin.class.php');

/**
 * Class backup_tool_devcourse_plugin.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_tool_devcourse_plugin extends backup_tool_plugin {

    /**
     * The name of the plugin associated with this backup class.
     *
     * @var string
     */
    protected $pluginname = 'tool_devcourse';

    /**
     * Defines the backup plugin_tool_devcourse_course structure inside of course.xml
     *
     * @return backup_plugin_element
     */
    protected function define_course_plugin_structure() {
        $plugin = $this->get_plugin_element();

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginwrapper);

        $pluginentry = new backup_nested_element('entry', ['id'], [
            'courseid', 'name', 'completed', 'priority', 'timecreated', 'timemodified', 'description', 'descriptionformat',
        ]);
        $pluginwrapper->add_child($pluginentry);

        $pluginentry->set_source_table($this->pluginname, ['courseid' => backup::VAR_COURSEID]);

        return $plugin;
    }

}
