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
 * Tests for the events of tool_devcourse plugin.
 *
 * @package    tool_devcourse
 * @category   test
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_devcourse;
use advanced_testcase;

/**
 * Unit tests for event handling in the devcourse tool.
 *
 * This class contains test cases to verify the correct behavior of events
 * within the devcourse admin tool in Moodle Workplace.
 *
 * @package    tool_devcourse
 * @category   test
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {

    /**
     * Sets up the environment before each test.
     *
     * This method is called before each test is executed. It can be used to initialize
     * objects, set up database fixtures, or perform any other setup required for the tests.
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Tests that an entry is correctly created and the appropriate event is triggered.
     *
     * This test verifies the behavior of the system when a new entry is created,
     * ensuring that the appropriate event is fired and any related logic is executed as expected.
     *
     * @covers \tool_devcourse\event\entry_created
     */
    public function test_entry_created(): void {
        $course = $this->getDataGenerator()->create_course();

        // Trigger Event.
        $sink = $this->redirectEvents();
        $entryid = \tool_devcourse\api::insert((object) [
            'courseid'  => $course->id,
            'name' => 'Test entry',
            'completed' => 0,
            'priority' => 0,
            'description' => 'Entry description',
        ]);
        $events = $sink->get_events();
        $event = reset($events);

        // Assert that the event is of the expected type and contains the correct data.
        $this->assertInstanceOf('\\tool_devcourse\\event\\entry_created', $event);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($entryid, $event->objectid);
    }

    /**
     * Tests that the entry_updated event is triggered and handled correctly.
     *
     * This test verifies the behavior of the system when an entry is updated,
     * ensuring that the appropriate event is fired and any related logic is executed as expected.
     *
     * @covers \tool_devcourse\event\entry_updated
     */
    public function test_entry_updated(): void {
        $course = $this->getDataGenerator()->create_course();

        $entryid = \tool_devcourse\api::insert((object) [
            'courseid'  => $course->id,
            'name' => 'Test entry',
            'completed' => 0,
            'priority' => 0,
            'description' => 'Entry description',
        ]);

        // Retrieve the full record to get all required fields.
        $entry = \tool_devcourse\api::retrieve($entryid);

        // Trigger Event.
        $sink = $this->redirectEvents();

        // We perform the update.
        \tool_devcourse\api::update((object) [
            'id' => $entryid,
            'courseid' => $entry->courseid,
            'name' => 'Test entry 2',
            'completed' => $entry->completed,
            'priority' => $entry->priority,
            'description' => $entry->description,
            'descriptionformat' => isset($entry->descriptionformat) ? $entry->descriptionformat : 1,
            'timemodified' => time(),
        ]);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Assert that the event is of the expected type and contains the correct data.
        $this->assertInstanceOf('\\tool_devcourse\\event\\entry_updated', $event);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($entryid, $event->objectid);
    }

    /**
     * Tests the event triggered when an entry is deleted.
     *
     * This test verifies that the appropriate event is fired and handled correctly
     * when an entry is deleted within the system.
     *
     * @covers \tool_devcourse\event\entry_deleted
     */
    public function test_entry_deleted(): void {
        $course = $this->getDataGenerator()->create_course();
        $entryid = \tool_devcourse\api::insert((object) [
            'courseid'  => $course->id,
            'name' => 'Test entry',
            'completed' => 0,
            'priority' => 0,
            'description' => 'Entry description',
        ]);

        // Trigger Event.
        $sink = $this->redirectEvents();
        \tool_devcourse\api::delete($entryid);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Assert that the event is of the expected type and contains the correct data.
        $this->assertInstanceOf('\\tool_devcourse\\event\\entry_deleted', $event);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($entryid, $event->objectid);
    }

}
