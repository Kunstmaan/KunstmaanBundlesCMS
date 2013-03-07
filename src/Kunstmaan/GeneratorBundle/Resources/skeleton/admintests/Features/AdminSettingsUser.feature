Feature: Browse the admin and perform CRUD on an admin user
  To test the user CRUD
  As an admin user
  A new user has to be created, updated, be able to log in and be deleted

  @javascript
  Scenario: Create a new user
    Given I am on "/en/admin"
    When I fill in "username" with "admin"
    When I fill in "password" with "admin"
    And I press "_submit"
    Then I should see "Dashboard"
    And I follow "Settings"
    Then I should be on "/en/admin/settings/"
    And I follow "Users"
    Then I should be on "/en/admin/settings/users"
    And I follow "Add New user"
    Then I should see "Add user"
    When I press "Add User"
    Then I should see "This value should not be blank."
    When I fill in "user[username]" with "test"
    When I fill in "user[plainPassword][first]" with "test"
    When I fill in "user[plainPassword][second]" with "notest"
    When I press "Add User"
    Then I should see "The passwords don't match!"
    When I fill in "user[plainPassword][first]" with "test"
    When I fill in "user[plainPassword][second]" with "test"
    When I fill in "user[email]" with "nomail"
    When I press "Add User"
    Then I should see "This value is not a valid email address."
    When I fill in "user[plainPassword][first]" with "test"
    When I fill in "user[plainPassword][second]" with "test"
    When I fill in "user[email]" with "support@kunstmaan.be"
    When I check "user[enabled]"
    When I select "Administrators" from "user[groups][]"
    When I press "Add User"
    Then I should see "Success: User 'test' has been created!"

  @javascript
  Scenario: Login as the newly created user
    Given I am on "/en/admin"
    When I fill in "username" with "test"
    When I fill in "password" with "test"
    And I press "_submit"
    Then I should see "Dashboard"