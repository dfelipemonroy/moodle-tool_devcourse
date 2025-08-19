@tool @tool_devcourse @javascript
Feature: Creating, editing and deleting entries.
    In order to manage entries
    As a teacher
    I need to be able to add, edit and delete entries

    Background:
        Given the following "users" exist:
            | username | firstname | lastname | email                |
            | teacher1 | Teacher   | 1        | teacher1@example.com |
        And the following "courses" exist:
            | fullname | shortname | format |
            | Course 1 | C1        | weeks  |
        And the following "course enrolments" exist:
            | user     | course | role           |
            | teacher1 | C1     | editingteacher |

    Scenario: Add and edit an entry.
        When I log in as "teacher1"
        And I follow "Course 1"
        And I navigate to "Dev course example" in current page administration
        And I follow "New entry"
        And I set the following fields to these values:
            | Name        | test entry 1 |
            | Completed   | 0            |
            | Description | cat          |
        And I press "Save changes"
        Then the following should exist in the "tool_devcourse_overview" table:
            | Name         | Completed | Description |
            | test entry 1 | No        | cat         |
        And I click on "Edit" "link" in the "test entry 1" "table_row"
        And I set the following fields to these values:
            | Completed | 1            |
        And I press "Save changes"
        And the following should exist in the "tool_devcourse_overview" table:
            | Name         | Description | Completed |
            | test entry 1 | cat         | Yes       |
        And I log out

    Scenario: Delete an entry.
        When I log in as "teacher1"
        And I follow "Course 1"
        And I navigate to "Dev course example" in current page administration
        And I follow "New entry"
        And I set the field "Name" to "test entry 1"
        And I press "Save changes"
        And I follow "New entry"
        And I set the field "Name" to "test entry 2"
        And I press "Save changes"
        And I click on "Delete" "link" in the "test entry 1" "table_row"
        Then I should see "test entry 2"
        And I should not see "test entry 1"
        And I log out
