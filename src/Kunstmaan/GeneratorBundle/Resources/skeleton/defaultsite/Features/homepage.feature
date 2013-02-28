Feature: Browse the homepage
  to test the homepage's availability
  As a website user
  "Welcome" has to be shown

  @javascript
  Scenario: Browse the homepage
    Given I am on "/"
    Then I should see "Welcome"
