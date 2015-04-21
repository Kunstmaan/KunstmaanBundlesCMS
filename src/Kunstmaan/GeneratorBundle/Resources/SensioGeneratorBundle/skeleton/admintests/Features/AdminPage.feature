@clean_session
Feature: AdminPage
  Make use of pages
  As an admin user
  User has to create, delete, preview pages

  Background:
    Given I log in as "admin"

  @javascript
  Scenario: Preview the home page
    Given I preview the home page
    Then I should not see "Not found"

  @javascript @ensureCleanSession
  Scenario: Add a new page
    Given I am on the admin home page
    And I add contentpage "ContentsubPage"
    Then I should see "ContentsubPage"

  @javascript
  Scenario: Save a page
    Given I save page "ContentsubPage"
    Then I should see "has been edited"

  @javascript
  Scenario: Save as draft
    Given I save page "ContentsubPage" as draft
    Then I should see "Page has been edited"
    And I should see "Draft version"

  @javascript @ensureCleanSession
  Scenario: Navigate to the page
    Given I go to page "/contentsubpage"
    Then I should see "Not found"

  @javascript
  Scenario: Navigate to the preview of the page
    Given I go to page "/admin/preview/contentsubpage"
    Then I should not see "Not found"

  @javascript
  Scenario: Publish the page
    Given I publish page "ContentsubPage"
    Then I should see "has been published"

  #ensureCleanSession is required to clear the cache
  @javascript @ensureCleanSession
  Scenario: Navigate to the page
    Given I go to page "/contentsubpage"
    Then I should not see "Not found"

  @javascript
  Scenario: Unpublish the page
    Given I unpublish page "ContentsubPage"
    Then I should see "has been unpublished"

  @javascript
  Scenario: Use filter module
    Given I am on the pages page
    And I filter on "Title" that "contains" "Contentsub"
    And I press "Filter"
    Then I should see "ContentsubPage"

  @javascript
  Scenario: Delete a page
    Given I delete page "ContentsubPage"
    Then I should see "The page is deleted"

  @javascript @ensureCleanSession
  Scenario: Navigate to the page
    Given I go to page "/contentsubpage"
    Then I should see "Not found"

  @javascript
  Scenario: Navigate to the preview of the page
    Given I go to page "/admin/preview/contentsubpage"
    Then I should see "Not found"
