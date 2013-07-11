<?php

namespace Sandbox\SandboxBundle\Features\Context;

use Kunstmaan\BehatBundle\Features\Context\FeatureContext as AbstractContext;
use Behat\Behat\Context\Step;

class FeatureContext extends AbstractContext
{

    public function __construct(array $parameters) {
        $this->parameters = $parameters;

        // Load Context Class
        $this->useContext('group_context', new GroupContext($parameters));
        $this->useContext('user_context', new UserContext($parameters));
        $this->useContext('role_context', new RoleContext($parameters));
        $this->useContext('media_context', new MediaContext($parameters));
        $this->useContext('page_context', new PageContext($parameters));
    }

    /**
     * @Given /^I log in as "([^\']*)"$/
     */
    public function iLogInAs($username)
    {
        $this->makeWide();

        $password = $this->getPasswordForUsername($username);

        return $this->iTryToLogInWith($username, $password);
    }

    public function getPasswordForUsername($username)
    {
        $logins = array('admin' => 'admin', 'test' => 'test', 'dummy' => 'dummy');

        return $logins[$username];
    }

    /**
     * @Given /^I try to log in with "([^"]*)" and "([^"]*)"$/
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
     * @Given /^I (?:am on|go to) the (.*) page$/
     */
    public function iAmOnASpecificPage($page)
    {
        $page = $this->fixStepArgument($page);
        $this->visit($this->getPageUrlForPageName($page));
    }


    public function getPageUrlForPageName($page) {
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

        return $pages[$page];
    }

    /**
     * @Given /^I clear "([^"]*)"$/
     */
    public function iClear($field)
    {
        $field = $this->fixStepArgument($field);

        return array(
            new Step\Given("I fill in \"$field\" with \"\"")
        );
    }

    public function clickAction($name, $action, $page)
    {
        $this->iAmOnASpecificPage($page);

        $name = $this->fixStepArgument($name);
        $action = ucfirst($this->fixStepArgument($action));

        $page = $this->getSession()->getPage();

        $td = $page->find('xpath', '//div[@class="content"]//table//td[text()="' . $name . '"]');
        $tr = $td->getParent();
        $deleteLink = $tr->find('xpath', '//a[text()="' . $action . '"]');

        $deleteLink->click();
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
