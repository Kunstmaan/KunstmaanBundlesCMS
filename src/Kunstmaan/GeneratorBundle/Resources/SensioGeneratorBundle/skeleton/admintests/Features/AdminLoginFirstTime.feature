@clean_session
Feature: AdminLoginFirstTime
  Browse and fill in the admin login form
  As an admin user
  User has to change his password

@javascript
Scenario: log in for the first time and change password
Given I log in for the first time as "admin"
And I am on page "admin"
Then I should see "not yet been changed"
When I change the password to "admin"
Then I should see "has been changed"