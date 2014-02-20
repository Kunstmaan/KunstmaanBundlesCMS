@clean_session @{{ name }}
Feature: {{ name }}
  Make use of pages and pageparts
  As an admin user
  User has to create, update, delete pageparts

  Background:
    Given I log in as "admin"

  @javascript
  Scenario: Fully test the pagepart
    Given I am on the admin home page

    # create a BehatTestPage and publish it
    Given I add behattestpage "BehatTestPage"
    And I save the current page
    Then I should see "has been edited"
    Given I publish the current page
    Then I should see "has been published"

{% for page in pages %}
    ############### "{{ page.name }}{{ loop.index }}" - "{{ page.template }}" - "{{ page.section }}" start ###############

    # create a new {{ page.name }} page as sub page of the BehatTestPage
    Then I go to admin page "BehatTestPage"
    And I add {{ page.name|lower }} "{{ page.name }}{{ loop.index }}"
    Then I should see "{{ page.name }}{{ loop.index }}"

    # fill in page properties
{% for fieldSet in page.fields %}{% for key, fieldArray in fieldSet %}
{% if key == 'text' %}
    And I fill in spaced field "{{ fieldArray['label'] }}" with "{{ fieldArray['random'] }}"
{% elseif key == 'rich_text' %}
    And I fill in pp cke field "{{ fieldArray['fieldName'] }}" with "<b>{{ fieldArray['random'] }}</b>"
{% elseif key == 'link' %}
    And I fill in spaced field "{{ fieldArray['label'] }}" with "{{ fieldArray['random'] }}"
{% elseif key == 'media' %}
    And I fill in pp image field "{{ fieldArray['label'] }}" with "{{ fieldArray['random'] }}"
{% elseif key == 'boolean' %}
    And I check "{{ fieldArray['label'] }}"
{% elseif key == 'integer' or key == 'decimal' %}
    And I fill in spaced field "{{ fieldArray['label'] }}" with "{{ fieldArray['random'] }}"
{% elseif key == 'datetime' %}
    And I fill in pp datetime field "{{ fieldArray['label'] }}" with "{{ fieldArray['date_random'] }}" "{{ fieldArray['time_random'] }}"
{% endif %}
{% endfor %}{% endfor %}

    # change the pagetemplate
    Then I change page template "{{ page.template }}"
    Then I should see "has been edited"

    # add the pagepart
    And I add pp "{{ name|replace({'PagePart': ''}) }}" in section "{{ page.section }}"
    And I wait 2 seconds

{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}
{% if key == 'single_line' or key == 'multi_line' %}
    And I fill in spaced field "{{ fieldArray[0]['lName'] }}" with "{{ fieldArray[0]['random1'] }}"
{% elseif key == 'rich_text' %}
    And I fill in pp cke field "{{ fieldArray[0]['fieldName'] }}" with "<b>{{ fieldArray[0]['random1'] }}</b>"
{% elseif key == 'link' %}
    And I fill in spaced field "{{ fieldArray['url']['lName'] }}" with "{{ fieldArray['url']['random1'] }}"
    And I fill in spaced field "{{ fieldArray['text']['lName'] }}" with "{{ fieldArray['text']['random1'] }}"
    And I check "{{ fieldArray['new_window']['lName'] }}"
{% elseif key == 'image' %}
    And I fill in pp image field "{{ fieldArray['image']['lName'] }}" with "{{ fieldArray['image']['id_random1'] }}"
    And I fill in spaced field "{{ fieldArray['alt_text']['lName'] }}" with "{{ fieldArray['alt_text']['random1'] }}"
{% elseif key == 'boolean' %}
    And I check "{{ fieldArray[0]['lName'] }}"
{% elseif key == 'integer' or key == 'decimal' %}
    And I fill in spaced field "{{ fieldArray[0]['lName'] }}" with "{{ fieldArray[0]['random1'] }}"
{% elseif key == 'datetime' %}
    And I fill in pp datetime field "{{ fieldArray[0]['lName'] }}" with "{{ fieldArray[0]['date_random1'] }}" "{{ fieldArray[0]['time_random1'] }}"
{% endif %}
{% endfor %}{% endfor %}

    # save an publish the page
    Given I save the current page
    Then I should see "has been edited"
    Given I publish the current page
    Then I should see "has been published"

    # check the public page
    Given I go to page "/behattestpage/{{ page.name|lower }}{{ loop.index }}"
    Then I should not see "page you requested could not be found"

{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}
{% if key == 'single_line' or key == 'multi_line' or key == 'rich_text' %}
    And I should see "{{ fieldArray[0]['random1'] }}"
{% elseif key == 'link' %}
    #And I should see link "http://www.google.be" with name "link"
    And I should see link "{{ fieldArray['url']['random1'] }}" with name "{{ fieldArray['text']['random1'] }}" that opens in a new window
{% elseif key == 'image' %}
    And I should see image "{{ fieldArray['image']['url_random1'] }}?v1" with alt text "{{ fieldArray['alt_text']['random1'] }}"
{% elseif key == 'boolean' %}
    And I should see "yes"
{% elseif key == 'integer' or key == 'decimal' %}
    And I should see "{{ fieldArray[0]['random1'] }}"
{% elseif key == 'datetime' %}
    And I should see "{{ fieldArray[0]['datetime_random1'] }}"
{% endif %}
{% endfor %}{% endfor %}

    # edit the pagepart in the admin interface
    Then I go to admin page "BehatTestPage"
    Then I click on admin page "{{ page.name }}{{ loop.index }}"
    And I edit pagepart "{{ name|replace({'PagePart': ''}) }}"
    And I wait 2 seconds

{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}
{% if key == 'single_line' or key == 'multi_line' %}
    And I fill in spaced field "{{ fieldArray[0]['lName'] }}" with "{{ fieldArray[0]['random2'] }}"
{% elseif key == 'rich_text' %}
    And I fill in pp cke field "{{ fieldArray[0]['fieldName'] }}" with "<b>{{ fieldArray[0]['random2'] }}</b>"
{% elseif key == 'link' %}
    And I fill in spaced field "{{ fieldArray['url']['lName'] }}" with "{{ fieldArray['url']['random2'] }}"
    And I fill in spaced field "{{ fieldArray['text']['lName'] }}" with "{{ fieldArray['text']['random2'] }}"
    And I check "{{ fieldArray['new_window']['lName'] }}"
{% elseif key == 'image' %}
    And I fill in pp image field "{{ fieldArray['image']['lName'] }}" with "{{ fieldArray['image']['id_random2'] }}"
    And I fill in spaced field "{{ fieldArray['alt_text']['lName'] }}" with "{{ fieldArray['alt_text']['random2'] }}"
{% elseif key == 'boolean' %}
    And I check "{{ fieldArray[0]['lName'] }}"
{% elseif key == 'integer' or key == 'decimal' %}
    And I fill in spaced field "{{ fieldArray[0]['lName'] }}" with "{{ fieldArray[0]['random2'] }}"
{% elseif key == 'datetime' %}
    And I fill in pp datetime field "{{ fieldArray[0]['lName'] }}" with "{{ fieldArray[0]['date_random2'] }}" "{{ fieldArray[0]['time_random2'] }}"
{% endif %}
{% endfor %}{% endfor %}

    # save an publish the page
    Given I save the current page
    Then I should see "has been edited"

    # check the public page
    Given I go to page "/behattestpage/{{ page.name|lower }}{{ loop.index }}"
    Then I should not see "page you requested could not be found"

{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}
{% if key == 'single_line' or key == 'multi_line' or key == 'rich_text' %}
    And I should see "{{ fieldArray[0]['random2'] }}"
{% elseif key == 'link' %}
    #And I should see link "http://www.google.be" with name "link"
    And I should see link "{{ fieldArray['url']['random2'] }}" with name "{{ fieldArray['text']['random2'] }}" that opens in a new window
{% elseif key == 'image' %}
    And I should see image "{{ fieldArray['image']['url_random2'] }}?v1" with alt text "{{ fieldArray['alt_text']['random2'] }}"
{% elseif key == 'boolean' %}
    And I should see "yes"
{% elseif key == 'integer' or key == 'decimal' %}
    And I should see "{{ fieldArray[0]['random2'] }}"
{% elseif key == 'datetime' %}
    And I should see "{{ fieldArray[0]['datetime_random2'] }}"
{% endif %}
{% endfor %}{% endfor %}

    # delete the pagepart
    Then I go to admin page "BehatTestPage"
    Then I click on admin page "{{ page.name }}{{ loop.index }}"
    And I delete pagepart "{{ name|replace({'PagePart': ''}) }}"
    Given I save the current page
    Then I should see "has been edited"

    # check the public page that the pagepart is deleted
    Given I go to page "/behattestpage/{{ page.name|lower }}{{ loop.index }}"
    Then I should not see "page you requested could not be found"

{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}
{% if key == 'single_line' or key == 'multi_line' or key == 'rich_text' %}
    And I should not see "{{ fieldArray[0]['random2'] }}"
{% elseif key == 'link' %}
    #todo
{% elseif key == 'image' %}
     #todo
{% elseif key == 'boolean' %}
    And I should not see "yes"
{% elseif key == 'integer' or key == 'decimal' %}
    And I should not see "{{ fieldArray[0]['random2'] }}"
{% elseif key == 'datetime' %}
    And I should not see "{{ fieldArray[0]['datetime_random2'] }}"
{% endif %}
{% endfor %}{% endfor %}

    ############### "{{ page.name }}{{ loop.index }}" - "{{ page.template }}" - "{{ page.section }}" end ###############

{% endfor %}

  # delete the BehatTestPage
  @javascript
  Scenario: Delete the BehatTestPage
    Given I delete page "BehatTestPage"
    Then I should see "The page is deleted"