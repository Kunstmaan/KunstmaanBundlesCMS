Feature: Browse and fill in the admin login form
  To test the login to the admin
  As an admin user
  User has to log in and see the Dashboard

  @javascript
  Scenario: Log in
    Given I am on "/en/admin"
    Then I should see "Username"
    When I fill in "username" with "naam"
    And I press "_submit"
    Then I should see "Wrong"
    When I fill in "username" with "admin"
    When I fill in "password" with "admin"
    And I press "_submit"
    Then I should see "Dashboard"
