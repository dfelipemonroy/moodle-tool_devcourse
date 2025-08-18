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
 * Run all upgrade steps between the current DB version and the current version on disk.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrades the database schema for the tool_devcourse plugin.
 *
 * This function is called automatically during the upgrade process to apply
 * any necessary changes to the database structure or data for the tool_devcourse
 * plugin, based on the current version.
 *
 * @param int $oldversion The version number of the currently installed plugin.
 * @return bool True on success, false on failure.
 */
function xmldb_tool_devcourse_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();
    $pluginname = 'tool_devcourse';

    if ($oldversion < 2025080705) {

        // Define table tool_devcourse to be created.
        $table = new xmldb_table($pluginname);

        // Adding fields to table tool_devcourse.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('priority', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_devcourse.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

        // Conditionally launch create table for tool_devcourse.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Devcourse savepoint reached.
        upgrade_plugin_savepoint(true, 2025080705, 'tool', 'devcourse');
    }

    if ($oldversion < 2025080706) {

        // Define index courseidname (unique) to be added to tool_devcourse.
        $table = new xmldb_table($pluginname);
        $index = new xmldb_index('courseidname', XMLDB_INDEX_UNIQUE, ['courseid', 'name']);

        // Conditionally launch add index courseidname.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Devcourse savepoint reached.
        upgrade_plugin_savepoint(true, 2025080706, 'tool', 'devcourse');
    }

    if ($oldversion < 2025081200) {

        // Define field description to be added to tool_devcourse.
        $table = new xmldb_table($pluginname);
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'priority');

        // Conditionally launch add field description.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field descriptionformat to be added to tool_devcourse.
        $field = new xmldb_field('descriptionformat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'description');

        // Conditionally launch add field descriptionformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Devcourse savepoint reached.
        upgrade_plugin_savepoint(true, 2025081200, 'tool', 'devcourse');
    }

    return true;
}
