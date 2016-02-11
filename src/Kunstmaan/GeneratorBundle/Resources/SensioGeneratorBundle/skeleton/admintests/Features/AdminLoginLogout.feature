@clean_session
Feature: AdminLoginLogout
  Browse and fill in the admin login form
  As an admin user
  User has to log in and see the Dashboard

  @javascript @resetBrowserAfter
  Scenario: try log in with incorrect credentials
    Given I try to log in with "name" and "wrong_password"
    Then I should see "Wrong"

  @javascript
  Scenario: log in with new credentials
    Given I log in as "admin"
    And I go to the dashboard page
    Then I should see the dashboard

  @javascript
  Scenario: log out when logged in
    Given I log in as "admin"
    And I log out
    Then I should be on the login page

  @javascript @ensureCleanSession
  Scenario: log out and navigate to page required to login
    Given I log in as "admin"
    And I log out
    And I go to the users page
    Then I should be on the login page