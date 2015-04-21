# TODO: Before the Feature runs we should delete the test user if it exists.
# Should actually run on test DB and run all the fixtures + run everything in a big transaction so
# we can roll back after every Feature.
@clean_session
Feature: AdminSettingsUser
  Browse the admin and perform CRUD on an admin user
  As an admin user
  A new user has to be created, updated, be able to log in and be deleted

  Background:
    Given I log in as "admin"

  @javascript
  Scenario: Can't create a new user without email
    Given I am on the create new user page
    And I fill in correct user information for username "dummy"
    And I clear "user[email]"
    When I press "Add User"
    Then I should see "Please enter an email"

  @javascript
  Scenario: Can't create a new user without valid email
    Given I am on the create new user page
    And I fill in correct user information for username "dummy"
    When I fill in "user[email]" with "dummy"
    When I press "Add User"
    Then I should see "email is not valid"

  @javascript
  Scenario: Can't create a new user without username
    Given I am on the create new user page
    And I fill in correct user information for username "dummy"
    And I clear "user[username]"
    When I press "Add User"
    Then I should see "Please enter a username"

  @javascript
  Scenario: Can't create a new user without password
    Given I am on the create new user page
    And I fill in correct user information for username "dummy"
    And I clear "user[plainPassword][first]"
    When I press "Add User"
    Then I should see "passwords you entered don't match"

  @javascript
  Scenario: Can't create a new user without matching passwords
    Given I am on the create new user page
    And I fill in correct user information for username "dummy"
    When I fill in "user[plainPassword][first]" with "1"
    When I fill in "user[plainPassword][second]" with "2"
    When I press "Add User"
    Then I should see "passwords you entered don't match"

  @javascript
  Scenario: Create a new user and try login
    Given I am on the create new user page
    And I fill in correct user information for username "test"
    When I press "Add User"
    Then I should see "has been created"
    When I log out
    And I log in as "test"
    Then I should see the dashboard

  @javascript
  Scenario: Login and edit own user
    Given I log out
    And I log in as "test"
    And I follow "Test"
    Then I should see "Settings"
    When I fill in "user[email]" with "support-edited@kunstmaan.be"
    And I press "Edit User"
    Then I should see "has been edited"

  @javascript
  Scenario: Login as admin, disable test user
    Given I edit user "test"
    When I uncheck "user[enabled]"
    And I press "Edit User"
    Then I should see "has been edited"

  @javascript @resetBrowserAfter
  Scenario: Try to log in as the previously disabled test user
    Given I log in as "test"
    Then I should not see the dashboard

  @javascript
  Scenario: Use filter module
    Given I am on the users page
    And I filter on "Username" that "equals" "test"
    And I additionally filter on "E-Mail" that "not equals" "guest@domain"
    And I press "Filter"
    Then I should see "test"

  @javascript
  Scenario: Login as admin, delete test user
    Given I delete user "test"
    Then I should see "has been deleted"
