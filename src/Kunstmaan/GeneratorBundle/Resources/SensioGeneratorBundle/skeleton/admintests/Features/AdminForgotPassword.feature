@clean_session
Feature: AdminForgotPassword
  Browse and fill in the forgot password form
  As a user
  User has to be able to reset the password

  @javascript
  Scenario: Follow the link to the form
    Given I am on the login page
    Then I should see "Forgot my password"
    When I follow "Forgot my password"
    Then I should see "Reset"

  @javascript
  Scenario: Provide no credentials
    Given I am on the forgot password page
    Then I should see "Username"
    When I press "Reset password"
    Then I should see "Error"

  @javascript
  Scenario: Provide wrong credentials
    Given I am on the forgot password page
    And I fill in "username" with "wrong_username"
    And I press "Reset password"
    Then I should see "Error"

  @javascript
  Scenario: Provide correct credentials
    Given I am on the forgot password page
    And I fill in "username" with "admin"
    And I press "Reset password"
    Then I should see "We have sent you" or "already been requested"

  @javascript
  Scenario: Use the cancel button
    Given I am on the forgot password page
    And I follow "Cancel"
    Then I should see "username"