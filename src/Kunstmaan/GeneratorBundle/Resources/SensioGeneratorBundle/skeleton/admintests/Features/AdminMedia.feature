# TODO: Test the bulk upload functionality
@clean_session
Feature: AdminMedia
  Make use of the media functionality
  As an admin user
  User has to upload file, slide, video, ...

  Background:
    Given I log in as "admin"

  @javascript
  Scenario Outline: Add a new file
    Given I am on the add new <fileTypePage> page
    And I fill in correct file information for <fileType>
    When I press "Add File"
    Then I should see "has been created"

  Examples:
    | fileTypePage | fileType |
    | image        | image    |
    | file         | pdf      |

  @javascript
  Scenario Outline: Login as admin, delete created image
    Given I delete <fileType>
    Then I should see "has been deleted"

  Examples:
    | fileType |
    | image    |
    | file     |

  @javascript
  Scenario Outline: Add a new videoType
    Given I am on the add new video page
    And I fill in correct <videoType> information for video <videoName>
    When I press "Add File"
    Then I should see "has been created"

  Examples:
    | videoType     | videoName     |
    | youtube       | "YouTube"     |
    | vimeo         | "Vimeo"       |
    | dailymotion   | "Dailymotion" |


  @javascript
  Scenario: Login as admin, delete a video
    Given I delete video
    Then I should see "has been deleted"

  @javascript
  Scenario: Add a new slideshare
    Given I am on the add new slide page
    And I fill in correct slideshare information for slide "Slideshare"
    When I press "Add File"
    Then I should see "has been created"

  @javascript
  Scenario: Login as admin, delete created slide
    Given I delete slide
    Then I should see "has been deleted"

  @javascript
  Scenario: Login as admin, create a subfolder
    Given I am on the media page
    And I create subfolder "subFolder"
    Then I should see "has been created"

  @javascript
  Scenario: Login as admin, delete a subfolder
    Given I am on the media page
    And I delete subfolder "subFolder"
    Then I should see "has been deleted"

  @javascript
  Scenario: Login as admin, delete a Media
    Given I am on the media page
    And I delete the folder
    Then I should see "can't delete"