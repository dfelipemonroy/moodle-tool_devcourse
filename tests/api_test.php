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
 * API tests for the tool_devcourse plugin.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_devcourse;
use advanced_testcase;

/**
 * API tests class for the tool_devcourse plugin.
 *
 * @package    tool_devcourse
 * @category   test
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
final class api_test extends advanced_testcase {

    /**
     * Sets up the environment before each test.
     *
     * This method is called before each test is executed. It can be used to initialize
     * objects, set up database fixtures, or perform any other setup required for the tests.
     *
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Tests the insert functionality of the API.
     *
     * This test verifies that the insert operation behaves as expected,
     * ensuring that data is correctly added to the system.
     *
     * @covers \tool_devcourse_api::insert
     * @covers \tool_devcourse_api::retrieve
     */
    public function test_insert(): void {
        $course = $this->getDataGenerator()->create_course();
        $entryid = \tool_devcourse_api::insert((object) [
            'courseid' => $course->id,
            'name' => 'testname1',
            'completed' => 1,
            'priority' => 0,
            'description' => 'description plain',
        ]);
        $entry = \tool_devcourse_api::retrieve($entryid);
        $this->assertEquals($course->id, $entry->courseid);
        $this->assertEquals('testname1', $entry->name);
        $this->assertEquals('description plain', $entry->description);
    }

    /**
     * Tests the update functionality of the API.
     *
     * This test verifies that the update operation performs as expected,
     * ensuring that the relevant data is correctly modified and any side
     * effects are handled appropriately.
     *
     * @covers \tool_devcourse_api::update
     * @covers \tool_devcourse_api::retrieve
     */
    public function test_update(): void {
        $course = $this->getDataGenerator()->create_course();
        $entryid = \tool_devcourse_api::insert((object) [
            'courseid' => $course->id,
            'name' => 'testname1',
        ]);
        $entry = \tool_devcourse_api::retrieve($entryid);
        \tool_devcourse_api::update((object) [
            'id' => $entryid,
            'courseid' => $entry->courseid,
            'name' => 'testname2',
            'completed' => isset($entry->completed) ? $entry->completed : 0,
            'priority' => isset($entry->priority) ? $entry->priority : 0,
            'description' => 'description updated',
            'descriptionformat' => isset($entry->descriptionformat) ? $entry->descriptionformat : 1,
        ]);
        $entry = \tool_devcourse_api::retrieve($entryid);
        $this->assertEquals($course->id, $entry->courseid);
        $this->assertEquals('testname2', $entry->name);
        $this->assertEquals('description updated', $entry->description);
    }

    /**
     * Tests the delete functionality of the API.
     *
     * This test verifies that the delete operation works as expected,
     * ensuring that the relevant data is properly removed and any
     * necessary cleanup is performed.
     *
     * @covers \tool_devcourse_api::delete
     * @covers \tool_devcourse_api::retrieve
     */
    public function test_delete(): void {
        $course = $this->getDataGenerator()->create_course();
        $entryid = \tool_devcourse_api::insert((object) [
            'courseid' => $course->id,
            'name' => 'testname1',
        ]);

        \tool_devcourse_api::delete($entryid);
        $entry = \tool_devcourse_api::retrieve($entryid, 0, IGNORE_MISSING);
        $this->assertFalse($entry);
    }

    /**
     * Tests the functionality of the description editor API.
     *
     * This test verifies that the description editor behaves as expected,
     * ensuring that the API processes and returns the correct data.
     *
     * @covers \tool_devcourse_api::insert
     * @covers \tool_devcourse_api::retrieve
     * @covers \tool_devcourse_api::update
     */
    public function test_description_editor(): void {
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $entryid = \tool_devcourse_api::insert((object)[
            'courseid' => $course->id,
            'name' => 'testname1',
            'description_editor' => [
                'text' => 'description formatted',
                'format' => FORMAT_HTML,
                'itemid' => file_get_unused_draft_itemid(),
            ],
        ]);
        $entry = \tool_devcourse_api::retrieve($entryid);
        $this->assertEquals('description formatted', $entry->description);
        \tool_devcourse_api::update((object) [
            'id' => $entryid,
            'courseid' => $entry->courseid,
            'name' => 'testname2',
            'completed' => isset($entry->completed) ? $entry->completed : 0,
            'priority' => isset($entry->priority) ? $entry->priority : 0,
            'description_editor' => [
                'text' => 'description edited',
                'format' => FORMAT_HTML,
                'itemid' => file_get_unused_draft_itemid(),
            ],
            'descriptionformat' => isset($entry->descriptionformat) ? $entry->descriptionformat : 1,
        ]);
        $entry = \tool_devcourse_api::retrieve($entryid);
        $this->assertEquals($course->id, $entry->courseid);
        $this->assertEquals('testname2', $entry->name);
        $this->assertEquals('description edited', $entry->description);
    }

}
