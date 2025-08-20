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
 * Library of functions for the Dev Course tool.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds this plugin to the course administration menu
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $context The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function tool_devcourse_extend_navigation_course($navigation, $course, $context) {
    $pluginname = 'tool_devcourse';
    if (has_capability('tool/devcourse:view', $context)) {
        $navigation->add(
            get_string('pluginname', $pluginname),
            new moodle_url('/admin/tool/devcourse/index.php', ['id' => $course->id]),
            navigation_node::TYPE_SETTING,
            get_string('pluginname', $pluginname),
            'devcourse',
            new pix_icon('icon', '', $pluginname));
    }
}

/**
 * Handles file serving for the devcourse plugin.
 *
 * This function is used to serve files associated with the devcourse plugin in Moodle.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param context $context The context of the file.
 * @param string $filearea The file area.
 * @param array $args Additional arguments for the file.
 * @param bool $forcedownload Whether or not to force download.
 * @param array $options Additional options affecting file serving.
 *
 * @return void|bool Outputs file content or sends appropriate headers, or false if not found.
 */
function tool_devcourse_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = []) {
    $pluginname = 'tool_devcourse';
    if ($context->contextlevel != CONTEXT_COURSE) {
        return false;
    }

    if ($filearea !== 'entry') {
        return false;
    }

    require_login($course);
    require_capability('tool/devcourse:view', $context);

    $itemid = array_shift($args);

    $entry = tool_devcourse_api::retrieve($itemid);

    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, $pluginname, $filearea, $itemid, $filepath, $filename);

    if (empty($file)) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
}
