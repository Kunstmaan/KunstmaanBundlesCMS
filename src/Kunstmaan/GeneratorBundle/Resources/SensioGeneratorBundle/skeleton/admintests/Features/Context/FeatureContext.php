<?php

namespace {{ namespace }}\Features\Context;

use Kunstmaan\BehatBundle\Features\Context\FeatureContext as AbstractContext;
use Behat\Behat\Context\Step;
use Behat\Mink\Selector\CssSelector;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * FeatureContext
 *
 * Provides the global context that includes the subcontexts
 */
class FeatureContext extends AbstractContext
{

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;

        // Load Context Class
        $this->useContext('group_context', new GroupContext($parameters));
        $this->useContext('user_context', new UserContext($parameters));
        $this->useContext('role_context', new RoleContext($parameters));
        $this->useContext('media_context', new MediaContext($parameters));
        $this->useContext('page_context', new PageContext($parameters));
    }

    /**
     * @param string $username
     *
     * @Given /^I log in as "([^\']*)"$/
     *
     * @return array
     */
    public function iLogInAs($username)
    {
        $this->makeWide();
        $password = $this->getPasswordForUsername($username);

        return $this->iTryToLogInWith($username, $password);
    }

    /**
     * @param string $username
     *
     * @return string
     */
    public function getPasswordForUsername($username)
    {
        $logins = array('admin' => 'admin', 'test' => 'test', 'dummy' => 'dummy');

        return $logins[$username];
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @Given /^I try to log in with "([^"]*)" and "([^"]*)"$/
     *
     * @return array
     */
    public function iTryToLogInWith($username, $password)
    {
        return array(
            new Step\Given("I am on \"/en/admin\""),
            new Step\Given("I press \"×\" if present"),
            new Step\Given("I fill in \"username\" with \"". $username . "\""),
            new Step\Given("I fill in \"password\" with \"" . $password . "\""),
            new Step\When("I press \"_submit\"")
        );
    }

    /**
     * @Given /^I log out$/
     */
    public function iLogOut()
    {
        $this->iAmOnASpecificPage('dashboard');
        $this->makeWide();
        $logoutButton = $this->getSession()->getPage()->find('xpath', '//a[text()="Logout"]');
        $logoutButton->click();
    }

    /**
     * @Then /^I should (?:be on|see) the dashboard$/
     */
    public function iShouldSeeTheDashboard()
    {
        $this->assertSession()->pageTextContains("Dashboard");
    }

    /**
     * @Then /^I should not (?:be on|see) the dashboard$/
     */
    public function iShouldNotSeeTheDashboard()
    {
        $this->assertSession()->pageTextNotContains("Dashboard");
    }

    /**
     * @param string $element the name of the button you want to press
     *
     * @Given /^I press "([^\']*)" if present$/
     */
    public function iPressIfPresent($element)
    {
        $locator = $this->fixStepArgument($element);
        $button = $this->getSession()->getPage()->findButton($locator);
        if (!is_null($button)) {
            $this->pressButton($element);
        }
    }

    /**
     * @param string $pageName
     *
     * @Given /^I (?:am on|go to) the (.*) page$/
     */
    public function iAmOnASpecificPage($pageName)
    {
        $pageName = $this->fixStepArgument($pageName);
        $this->visit($this->getPageUrlForPageName($pageName));
    }

    /**
     * @param string $pageName
     *
     * @return string
     */
    public function getPageUrlForPageName($pageName)
    {
        $pages = array(
            "users" => "/en/admin/settings/users",
            "create new user" => "/en/admin/settings/users/add",
            "groups" => "/en/admin/settings/groups",
            "create new group" => "/en/admin/settings/groups/add",
            "roles" => "en/admin/settings/roles",
            "create new role" => "en/admin/settings/roles/add",
            "dashboard" => "/en/admin",
            "login" => "/en/login",
            "media" => "/en/admin/media/folder/1",
            "add new image" => "/en/admin/media/create/2/file",
            "image" => "en/admin/media/folder/2",
            "add new video" => "en/admin/media/create/3/video",
            "video" => "en/admin/media/folder/3",
            "add new slide" => "en/admin/media/create/4/slide",
            "slide" => "en/admin/media/folder/4",
            "add new file" => "en/admin/media/create/5/file",
            "file" => "en/admin/media/folder/5",
            "bulkupload" => "/en/admin/media/bulkupload/1",
            "admin home" => "/en/admin/nodes/1",
            "home" => "/en/admin/nodes/1"
        );

        return $pages[$pageName];
    }

    /**
     * @param string $fieldName
     *
     * @Given /^I clear "([^"]*)"$/
     *
     * @return array
     */
    public function iClear($fieldName)
    {
        $fieldName = $this->fixStepArgument($fieldName);

        return array(
            new Step\Given("I fill in \"$fieldName\" with \"\"")
        );
    }

    /**
     * @param string $name     the name of the a tag
     * @param string $action   the action you want to perform - delete, edit
     * @param string $pageName the name of the page
     */
    public function clickAction($name, $action, $pageName)
    {
        $this->iAmOnASpecificPage($pageName);

        $name = $this->fixStepArgument($name);
        $action = ucfirst($this->fixStepArgument($action));

        $page = $this->getSession()->getPage();

        $td = $page->find('xpath', '//div[@class="content"]//table//td[text()="' . $name . '"]');
        $tr = $td->getParent();
        $deleteLink = $tr->find('xpath', '//a[text()="' . $action . '"]');

        $deleteLink->click();
    }

    /**
     * @param string $filterType
     * @param string $filterComparator
     * @param string $filterValue
     * @param bool   $additionally
     *
     * @Given /^I filter on "([^"]*)" that "([^"]*)" "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iFilterOn($filterType, $filterComparator, $filterValue, $additionally = false)
    {
        //Only activate the filter module if it is not an additionally filter
        if (!$additionally) {
            $selector = new CssSelector();
            $this->getSession()->getPage()->find("xpath", $selector->translateToXPath('div.iPhoneCheckHandle'))->click();
        }

        $records = $this->createFilterRecords($filterType, $filterComparator, $filterValue, $additionally);

        foreach ($records as $field => $value) {
            //We need this check when adding additionally filters
            //because the filter_columnname[] is the same for all the filter lines
            if ($additionally && $field=='filter_columnname[]') {
                $filterFields = $this->getSession()->getPage()->findAll('named', array('field', $this->getSession()->getSelectorsHandler()->xpathLiteral($field)));
                $filterField = $filterFields[count($filterFields)-1];
            } else {
                $filterField = $this->getSession()->getPage()->find('named', array('field', $this->getSession()->getSelectorsHandler()->xpathLiteral($field)));
            }
            if ($filterField === null) {
                throw new ElementNotFoundException(
                    $this->getSession(), 'form field', 'id|name|label|value', $field
                );
            }
            $filterField->setValue($value);
        }
    }

    /**
     * @param string $filterType
     * @param string $filterComparator
     * @param string $filterValue
     *
     * @Given /^I additionally filter on "([^"]*)" that "([^"]*)" "([^"]*)"$/
     */
    public function iAdditionallyFilterOn($filterType, $filterComparator, $filterValue)
    {
        $this->pressButton("Add filter");
        $this->iFilterOn($filterType, $filterComparator, $filterValue, true);
    }

    /**
     * @param string $filterType
     * @param string $filterComparator
     * @param string $filterValue
     * @param bool   $additionally
     *
     * @return array
     */
    private function createFilterRecords($filterType, $filterComparator, $filterValue, $additionally)
    {
        if ($additionally) {
            $selector = new CssSelector();
            //We need to know the number of filter lines present for the comparator and value field
            $nrOfFilterOptions = count($this->getSession()->getPage()->findAll("xpath", $selector->translateToXPath('form span.filteroptions')));

            return array(
                "filter_columnname[]" => $this->fixStepArgument($filterType),
                "filter_comparator_".$nrOfFilterOptions => $this->fixStepArgument($filterComparator),
                "filter_value_".$nrOfFilterOptions => $this->fixStepArgument($filterValue),
            );
        } else {
            return array(
                "addfilter" => $this->fixStepArgument($filterType),
                "filter_comparator_1" => $this->fixStepArgument($filterComparator),
                "filter_value_1" => $this->fixStepArgument($filterValue),
            );
        }
    }

    /**
     * Place this on a scenario that leaves the page in a faulty state that's difficult to recover.
     * This is for example placed on a scenario that fails to log in a user.
     *
     * @AfterScenario @resetBrowserAfter
     */
    public function resetBrowserAfter()
    {
        $this->getSession()->restart();
    }

    /**
     * @BeforeScenario @ensureCleanSession
     */
    public function ensureCleanSession()
    {
        $this->getSession()->restart();
    }

    /**
     * Make the window wider.
     *
     * @BeforeScenario @wide
     */
    public function makeWide()
    {
        $this->getSession()->resizeWindow(1400, 1000);
    }

    /**
     * @param int $time
     *
     * @Given /^I wait (\d+) seconds$/
     */
    public function iWaitSeconds($time)
    {
        $this->getSession()->wait($time*1000);
    }

    /**
     * Calls the protected fixStepArgument in the MinkContext
     *
     * @param string $argument
     *
     * @return string
     */
    public function fixStepArgument($argument)
    {
        return parent::fixStepArgument($argument);
    }
}
