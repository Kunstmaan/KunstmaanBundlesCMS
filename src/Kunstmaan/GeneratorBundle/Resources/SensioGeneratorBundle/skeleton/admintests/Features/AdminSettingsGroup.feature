@clean_session
Feature: AdminSettingsGroup
  Browse the admin and perform CRUD on an group
  As an admin user
  A new group has to be created, updated, be used and be deleted

  Background:
    Given I log in as "admin"

  @javascript
  Scenario: Create a new group
    Given I am on the create new group page
    And I fill in correct group information for group "test_group"
    When I press "Add group"
    Then I should see "has been created"

  @javascript
  Scenario: Can't create a new group without name
    Given I am on the create new group page
    And I fill in correct group information for group "test_group"
    And I clear "group[name]"
    When I press "Add group"
    Then I should see "value should not be blank"

  @javascript
  Scenario: Can't create a new group without roles
    Given I am on the create new group page
    And I fill in group information for group "test_group" without roles
    When I press "Add group"
    Then I should see "one option must be selected"

  @javascript
  Scenario: Login and edit group
    Given I edit group "test_group"
    Then I should see "Edit group"
    When I additionally select "IS_AUTHENTICATED_ANONYMOUSLY" from "group[rolesCollection][]"
    And I press "Edit group"
    Then I should see "has been edited"

  @javascript
  Scenario: Use filter module
    Given I am on the groups page
    And I filter on "name" that "starts with" "Admin"
    And I additionally filter on "name" that "ends with" "s"
    And I press "Filter"
    Then I should see "Administrators"
    And I should not see "Guests"

  @javascript
  Scenario: Login as admin, delete test group
    Given I delete group "test_group"
    Then I should see "has been deleted"
