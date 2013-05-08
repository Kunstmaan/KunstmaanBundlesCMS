Feature: Browse and fill in the admin login form
  To test the login to the admin
  As an admin user
  User has to log in and see the Dashboard

  @javascript @resetBrowserAfter
  Scenario: Can't log in with incorrect credentials
    Given I try to log in with "naam" and "wrongpassword"
    Then I should see "Wrong"

  @javascript
  Scenario: Can log in with correct credentials
    Given I log in as "admin"
    Then I should see the dashboard

  @javascript
  Scenario: Can log in with correct credentials
    Given I log in as "admin"
  # https://code.google.com/p/chromedriver/wiki/TroubleshootingAndSupport#Common_issues
    And I log out
    Then I should be on the login page

  @javascript @ensureCleanSession
  Scenario: Can log in with correct credentials
    Given I log in as "admin"
    And I log out
    And I go to the users page
    Then I should be on the login page
