# TODO: Before the Feature runs we should delete the test user if it exists.
# Should actually run on test DB and run all the fixtures + run everything in a big transaction so
# we can roll back after every Feature.

Feature: Browse the admin and perform CRUD on an admin user
  To test the user CRUD
  As an admin user
  A new user has to be created, updated, be able to log in and be deleted

  @javascript
  Scenario: Create a new user
    Given I log in as "admin"
    And I am on the create new user page
    And I fill in correct user information for username "test"
    When I press "Add User"
    Then I should see "has been created"

  @javascript
  Scenario: Can't create a new user without email
    Given I log in as "admin"
    And I am on the create new user page
    And I fill in correct user information for username "dummy"
    And I clear "user[email]"
    When I press "Add User"
    Then I should see "email is required"

  @javascript
  Scenario: Can't create a new user without valid email
    Given I log in as "admin"
    And I am on the create new user page
    And I fill in correct user information for username "dummy"
    When I fill in "user[email]" with "dummy"
    When I press "Add User"
    Then I should see "not a valid email address"

  @javascript
  Scenario: Can't create a new user without username
    Given I log in as "admin"
    And I am on the create new user page
    And I fill in correct user information for username "dummy"
    And I clear "user[username]"
    When I press "Add User"
    Then I should see "username is required"

  @javascript
  Scenario: Can't create a new user without password
    Given I log in as "admin"
    And I am on the create new user page
    And I fill in correct user information for username "dummy"
    And I clear "user[plainPassword][first]"
    When I press "Add User"
    Then I should see "password is required"

  @javascript
  Scenario: Can't create a new user without matching passwords
    Given I log in as "admin"
    And I am on the create new user page
    And I fill in correct user information for username "dummy"
    When I fill in "user[plainPassword][first]" with "1"
    When I fill in "user[plainPassword][second]" with "2"
    When I press "Add User"
    Then I should see "passwords don't match"

  @javascript
  Scenario: Login as the newly created user and edit self
    Given I log in as "test"
    And I edit user "test"
    Then I should see "Edit user"
    When I fill in "user[email]" with "support-edited@kunstmaan.be"
    And I press "Edit User"
    Then I should see "has been edited"

  @javascript
  Scenario: Login as admin, disable test user
    Given I log in as "admin"
    And I edit user "test"
    When I uncheck "user[enabled]"
    And I press "Edit User"
    Then I should see "has been edited"

  @javascript @resetBrowserAfter
  Scenario: Try to log in as the previously disabled test user
    Given I log in as "test"
    Then I should not see the dashboard

  @javascript
  Scenario: Login as admin, delete test user
    Given I log in as "admin"
    And I delete user "test"
    Then I should see "has been deleted"
