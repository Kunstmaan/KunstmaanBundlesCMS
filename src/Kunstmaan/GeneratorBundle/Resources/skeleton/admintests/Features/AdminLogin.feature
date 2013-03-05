Feature: Browse and fill in the admin login form
  To login in the admin
  As an admin user
  User has to log in and send the user to the Dashboard

  @javascript
  Scenario: Browse the admin login page
    Given I am on "/en/admin"
    Then I should see "Username"

  @javascript
  Scenario: Try to log in but fail
    Given I am on "/en/admin"
    When I fill in "username" with "naam"
    And I press "_submit"
    Then I should see "Wrong"

  @javascript
  Scenario: Try to log in
    Given I am on "/en/admin"
    When I fill in "username" with "admin"
    When I fill in "password" with "admin"
    And I press "_submit"
    Then I should see "Dashboard"