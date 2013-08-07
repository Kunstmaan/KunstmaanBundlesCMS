@clean_session
Feature: AdminSettingsRole
  Browse the admin and perform CRUD on an role
  As an admin user
  A new role has to be created, updated, be used and be deleted

  @javascript
  Scenario: Create a new role
    Given I log in as "admin"
    And I am on the create new role page
    And I fill in correct role information for role "TEST_ROLE"
    When I press "Add Role"
    Then I should see "has been created"

  @javascript
  Scenario: Can't create a new role without name
    Given I log in as "admin"
    And I am on the create new role page
    And I press "Add Role"
    Then I should see "value should not be blank"

  @javascript
  Scenario: Edit role
    Given I log in as "admin"
    And I edit role "TEST_ROLE"
    Then I should see "Edit role"
    When I fill in "role[role]" with "OTHER_ROLE_NAME"
    And I press "Edit Role"
    Then I should see "has been edited"

  @javascript
  Scenario: Use filter module
    Given I log in as "admin"
    And I am on the roles page
    And I filter on "role" that "contains" "ROLE"
    And I additionally filter on "role" that "doesn't contain" "GUEST"
    And I press "Filter"
    Then I should see "OTHER_ROLE_NAME"
    And I should not see "ROLE_GUEST"

  @javascript
  Scenario: Delete test role
    Given I log in as "admin"
    And I delete role "OTHER_ROLE_NAME"
    Then I should see "has been deleted"
