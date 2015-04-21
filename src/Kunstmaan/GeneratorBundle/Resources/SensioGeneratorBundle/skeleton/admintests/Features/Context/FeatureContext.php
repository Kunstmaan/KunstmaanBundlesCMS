<?php

namespace {{ namespace }}\Features\Context;

use Kunstmaan\BehatBundle\Features\Context\FeatureContext as AbstractContext;
use Behat\Behat\Context\Step;
use Behat\Mink\Element\Element;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Selector\CssSelector;

/**
 * FeatureContext
 *
 * Provides the global context that includes the subcontexts
 */
class FeatureContext extends AbstractContext
{

    /**
     * The language to prefix the url with e.g. /en/admin
     *
     * @var string
     */
    private $lang;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;

        if ($this->parameters['language'] === 'multi') {
            $this->lang = "/en";
        }

        // Load Context Class
        $this->useContext('group_context', new GroupContext($parameters));
        $this->useContext('user_context', new UserContext($parameters));
        $this->useContext('role_context', new RoleContext($parameters));
        $this->useContext('media_context', new MediaContext($parameters));
        $this->useContext('page_context', new PageContext($parameters));
        $this->useContext('pagepart_context', new PagePartContext($parameters));
    }

    /**
     * Fills in form field with specified id|name|label|value.
     * This function also allows spaces in field name and value
     *
     * @When /^I fill in spaced field "([^"]*)" with "([^"]*)"$/
     */
    public function iFillField($field, $value)
    {
        $this->fillField($field, $value);
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *This function also allows spaces in field name
     *
     * @When /^I check spaced field "([^"]*)"$/
     */
    public function iCheckOption($option)
    {
        $this->checkOption($option);
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
            new Step\Given("I am on \"".$this->lang."/admin/login\""),
            new Step\Given("I wait 1 seconds"),
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
	return array(
	    new Step\Given('I am on "/'.$this->lang.'/admin/dashboard"'),
	    new Step\Given('I wait 2 seconds'),
	    new Step\When('I follow "logout"')
	);
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
     * @param string $pageName
     *
     * @Then /^I should be on the (.*) page$/
     */
    public function iShouldBeOnTheLoginPage($pageName)
    {
        $pageName = $this->fixStepArgument($pageName);
        $this->assertPageAddress($this->getPageUrlForPageName($pageName));
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
     * @Given /^(?:|I )am on page "(?P<page>[^"]+)"$/
     * @When /^(?:|I )go to page "(?P<page>[^"]+)"$/
     */
    public function goToPage($pageName)
    {
        if (!empty($this->lang) && strncmp($pageName, "/", strlen("/"))) {
            $pageName = "/".$pageName;
        }
        $this->getSession()->visit($this->locatePath($this->lang.$pageName));
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
            "forgot password" => $this->lang."/admin/resetting/request",
            "users" => $this->lang."/admin/settings/users",
            "create new user" => $this->lang."/admin/settings/users/add",
            "groups" => $this->lang."/admin/settings/groups",
            "create new group" => $this->lang."/admin/settings/groups/add",
            "roles" => $this->lang."/admin/settings/roles",
            "create new role" => $this->lang."/admin/settings/roles/add",
            "dashboard" => $this->lang."/admin",
            "login" => $this->lang."/admin/login",
            "media" => $this->lang."/admin/media/folder/1",
            "add new image" => $this->lang."/admin/media/create/2/file",
            "image" => $this->lang."/admin/media/folder/2",
            "add new video" => $this->lang."/admin/media/create/3/video",
            "video" => $this->lang."/admin/media/folder/3",
            "add new slide" => $this->lang."/admin/media/create/4/slide",
            "slide" => $this->lang."/admin/media/folder/4",
            "add new file" => $this->lang."/admin/media/create/5/file",
            "file" => $this->lang."/admin/media/folder/5",
            "bulkupload" => $this->lang."/admin/media/bulkupload/1",
            "admin home" => $this->lang."/admin/nodes/1",
            "home" => $this->lang."/admin/nodes/1",
            "pages" => $this->lang."/admin/nodes"
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

        $page = $this->getMainContext()->getSession()->getPage();
        $row = $page->find('css', sprintf('table tr:contains("%s")', $name));
        if (!$row) {
            throw new \Exception(sprintf('Cannot find any row on the page containing the text "%s"', $name));
        }

        $row->clickLink($action);
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
            $page = $this->getMainContext()->getSession()->getPage();
            $this->getMainContext()->findAndClickButton($page, 'xpath', "//button[contains(@data-target, '#app__filter')]");
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
            //Check to see if we need to select an option or fill in a value.
            if ('select' !== $filterField->getTagName()) {
                $filterField->setValue($value);
            } else {
                $filterField->selectOption($value);
            }
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
            $nrOfFilterOptions = count($this->getSession()->getPage()->findAll("xpath", $selector->translateToXPath('form span.js-filter-options')));

            return array(
                "filter_columnname[]" => $this->fixStepArgument($filterType),
                "filter_comparator_".$nrOfFilterOptions => $this->fixStepArgument($filterComparator),
                "filter_value_".$nrOfFilterOptions => $this->fixStepArgument($filterValue),
            );
        } else {
            return array(
                "add-first-filter" => $this->fixStepArgument($filterType),
                "filter_comparator_1" => $this->fixStepArgument($filterComparator),
                "filter_value_1" => $this->fixStepArgument($filterValue),
            );
        }
    }

    /**
     * @Then /^I should see "([^"]*)" or "([^"]*)"$/
     *
     * @throws ResponseTextException
     */
    public function iShouldSeeOr($text1, $text2)
    {
        $actual = $this->getSession()->getPage()->getText();
        $regex1  = '/'.preg_quote($text1, '/').'/ui';
        $regex2  = '/'.preg_quote($text2, '/').'/ui';

        if (!(preg_match($regex1, $actual) || preg_match($regex2, $actual))) {
            $message = sprintf('The text "%s" was not found anywhere in the text of the current page.', $text2);
            throw new ResponseTextException($message, $this->getSession());
        }
    }

    /**
     * @Then /^I should see link "([^"]*)" with name "([^"]*)"$/
     *
     * @throws ResponseTextException
     */
    public function iShouldSeeLink($href, $value)
    {
        $this->checkLink($href, $value);
    }

    /**
     * @Then /^I should see link "([^"]*)"$/
     *
     * @throws ResponseTextException
     */
    public function iShouldSeeSimpleLink($href)
    {
        $this->checkLink($href);
    }

    /**
     * @Then /^I should see link "([^"]*)" with name "([^"]*)" that opens in a new window$/
     *
     * @throws ResponseTextException
     */
    public function iShouldSeeLinkNewWindow($href, $value)
    {
        $this->checkLink($href, $value, true);
    }

    /**
     * Check if link exists on page.
     *
     * @param string $href
     * @param string $value
     * @param bool $newWindow
     *
     * @return bool
     * @throws \Behat\Mink\Exception\ResponseTextException
     */
    protected function checkLink($href, $value = null, $newWindow = false) {
        $page = $this->getSession()->getPage();
        $element = $page->find('xpath', "//a[contains(@href, '" . $href . "')".($newWindow ? 'and contains(@target, "_blank")' : '')."]");
        if ($element) {
            if (!is_null($value)) {
                if ($value == $element->getText()) {
                    return true;
                }
            } else {
                return true;
            }
        }

        $message = sprintf('The link "%s" was not found anywhere on the current page.', $href);
        throw new ResponseTextException($message, $this->getSession());
    }

    /**
     * @Then /^I should see image "([^"]*)" with alt text "([^"]*)"$/
     *
     * @throws ResponseTextException
     */
    public function iShouldSeeImage($path, $altText = "")
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('xpath', "//img[contains(@src, '" . $path . "')".($altText ? 'and contains(@alt, "' . $altText . '")' : '')."]");
        if ($element) {
            return true;
        }

        $message = sprintf('The link "%s" was not found anywhere on the current page.', $path);
        throw new ResponseTextException($message, $this->getSession());
    }

    /**
     * Finds an element with specified selector and clicks it.
     *
     * @param Element $element  the element
     * @param string  $selector selector engine name e.g. xpath
     * @param string  $locator  selector locator
     *
     * @return array
     * @throws ExpectationException
     */
    public function findAndClickButton($element, $selector, $locator)
    {
        //Wait for the button to be visible
        $this->iWaitSeconds(1);
        $button = $element->find($selector, $locator);
        if (!is_null($button)) {
            $button->click();
        } else {
            $message = sprintf('The button was not found');
            throw new ExpectationException($message, $this->getSession());
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

    /**
     * @Then /^I scroll to top$/
     */
    public function iScrollToTop()
    {
        $this->getSession()->executeScript("window.scrollTo(0, 0)");
    }

    /**
     * @Then /^I scroll to bottom$/
     */
    public function iScrollToBottom()
    {
        $this->getSession()->executeScript("window.scrollTo(0, document.body.scrollHeight)");
    }

}
