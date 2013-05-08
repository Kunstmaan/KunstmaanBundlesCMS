<?php

namespace {{ namespace }}\Features\Context;

use Kunstmaan\BehatBundle\Features\Context\FeatureContext as AbstractContext;
use Behat\Behat\Context\Step;

class FeatureContext extends AbstractContext
{

    /**
     * @Given /^I log in as "([^\']*)"$/
     */
    public function iLogInAs($username)
    {
        $password = $this->getPasswordForUsername($username);

        return $this->iTryToLogInWith($username, $password);
    }

    protected function getPasswordForUsername($username)
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


    /**
     * @Then /^I should be on the (.*) page$/
     */
    public function iShouldBeOnTheLoginPage($page)
    {
        $page = $this->fixStepArgument($page);
        $this->assertPageAddress($this->getPageUrlForPageName($page));
    }


    private function getPageUrlForPageName($page) {
        $pages = array("create new user" => "/en/admin/settings/users/add",
            "users" => "/en/admin/settings/users",
            "dashboard" => "/en/admin",
            "login" => "/en/login",
        );

        return $pages[$page];
    }

    /**
     * @Given /^I fill in correct user information for username "([^\']*)"$/
     */
    public function iFillInCorrectUserInformation($username)
    {
        $username = $this->fixStepArgument($username);
        $password = $this->getPasswordForUsername($username);

        $records = array(
            "user[username]" => $username,
            "user[plainPassword][first]" => $password,
            "user[plainPassword][second]" => $password,
            "user[email]" => "support+" . $username . "@kunstmaan.be",
        );

        $steps = array();
        foreach($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        $steps[] = new Step\When("I check \"user[enabled]\"");
        $steps[] = new Step\When("I select \"Administrators\" from \"user[groups][]\"");

        return $steps;
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

    /**
     * @Given /^I edit user "([^"]*)"$/
     */
    public function iEditUser($username)
    {
        $this->clickActionOnUser($username, 'edit');
    }

    private function clickActionOnUser($username, $action) {
        $this->iAmOnASpecificPage("users");

        $username = $this->fixStepArgument($username);
        $action = ucfirst($this->fixStepArgument($action));

        $page = $this->getSession()->getPage();
        $td = $page->find('xpath', '//div[@class="content"]//table//td[text()="' . $username . '"]');
        $tr = $td->getParent();
        $deleteLink = $tr->find('xpath', '//a[text()="' . $action . '"]');

        $deleteLink->click();
    }

    /**
     * @Given /^I delete user "([^"]*)"$/
     */
    public function iDeleteUser($username)
    {
        $this->clickActionOnUser($username, 'delete');

        $page = $this->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@class, 'modal')]");

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $confirmButton = $modal->find('xpath', "//form//button[@type='submit']");
                $confirmButton->click();
                return;
            }
        }

    }


    /**
     * @Given /^I log out$/
     */
    public function iLogOut()
    {
        $this->iAmOnASpecificPage('dashboard');
        // $this->makeWide();

        $logoutButton = $this->getSession()->getPage()->find('xpath', '//a[text()="Logout"]');
        $logoutButton->click();
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
}
