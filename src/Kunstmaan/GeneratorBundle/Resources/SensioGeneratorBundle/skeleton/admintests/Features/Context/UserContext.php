<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

/**
 * UserContext
 *
 * Provides the context for the AdminSettingsUser.feature
 */
class UserContext extends BehatContext
{

    /**
     * @param string $username
     *
     * @Given /^I fill in correct user information for username "([^\']*)"$/
     *
     * @return array
     */
    public function iFillInCorrectUserInformation($username)
    {
        $username = $this->getMainContext()->fixStepArgument($username);
        $password = $this->getMainContext()->getPasswordForUsername($username);

        $records = array(
            "user[username]" => $username,
            "user[plainPassword][first]" => $password,
            "user[plainPassword][second]" => $password,
            "user[email]" => "support+" . $username . "@kunstmaan.be",
        );

        $steps = array();
        foreach ($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        $steps[] = new Step\When("I check \"user[enabled]\"");
        $steps[] = new Step\When("I select \"Administrators\" from \"user[groups][]\"");

        return $steps;
    }

    /**
     * @param string $username
     *
     * @Given /^I edit user "([^"]*)"$/
     */
    public function iEditUser($username)
    {
        $this->getMainContext()->clickAction($username, 'Edit', 'users');
    }

    /**
     * @param string $username
     *
     * @Given /^I delete user "([^"]*)"$/
     */
    public function iDeleteUser($username)
    {
        $this->getMainContext()->clickAction($username, 'Delete', 'users');

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@class, 'modal')]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->getMainContext()->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $this->getMainContext()->findAndClickButton($modal, 'xpath', "//form//button[@type='submit']");

                return;
            }
        }
    }
}
