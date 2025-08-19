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
 * Class tool_devcourse_renderer.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_devcourse\output\entries_list;

/**
 * Renderer class for the DevCourse admin tool plugin.
 *
 * Extends the core plugin_renderer_base to provide custom rendering
 * methods for the DevCourse tool within the Moodle Workplace environment.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_devcourse_renderer extends plugin_renderer_base {

    /**
     * Renders the list of entries.
     *
     * @param entries_list $list The list of entries to render.
     * @return string The rendered HTML output for the entries list.
     */
    protected function render_entries_list(entries_list $list) {
        $context = $list->export_for_template($this);
        return $this->render_from_template('tool_devcourse/entries_list', $context);
    }

}
