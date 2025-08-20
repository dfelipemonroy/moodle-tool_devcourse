@tool @tool_devcourse
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
    And the following config values are set as admin:
        | enabled  | 1      | tool_devcourse |

  @javascript
  Scenario: Add and edit an entry.
    When I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Admin Tool Moodle Dev Course" in current page administration
    And I follow "New entry"
    And I set the following fields to these values:
        | Name        | Test Entry        |
        | Completed   | 0                 |
        | Description | Entry Description |
    And I press "Save changes"
    Then the following should exist in the "tool_devcourse_overview" table:
        | Name       | Completed | Description       |
        | Test Entry | No        | Entry Description |
    When I click on "Edit" "link" in the "Test Entry" "table_row"
    And I set the following fields to these values:
        | Name        | Test Entry Updated        |
        | Completed   | 1                         |
        | Description | Entry Description Updated |
    And I press "Save changes"
    Then the following should exist in the "tool_devcourse_overview" table:
        | Name       | Completed | Description               |
        | Test Entry | Yes       | Entry Description Updated |

  @javascript
  Scenario: Delete an entry.
    When I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Admin Tool Moodle Dev Course" in current page administration
    And I follow "New entry"
    And I set the following fields to these values:
        | Name        | Test Entry        |
        | Completed   | 1                 |
        | Description | Entry Description |
    And I press "Save changes"
    And I click on "Delete" "link" in the "Test Entry" "table_row"
    And I press "Yes"
    Then I should not see "Test Entry"

  @javascript
  Scenario: Plugin is not enabled.
    Given the following config values are set as admin:
        | enabled  | 0      | tool_devcourse |
    When I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Admin Tool Moodle Dev Course" in current page administration
    Then I should not see "New entry"
    And I should see "The Tool Dev Course functionality is disabled, please contact your administrator."
