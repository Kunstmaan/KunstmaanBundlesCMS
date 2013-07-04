<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

class GroupContext extends BehatContext
{

    /**
     * @Given /^I fill in correct group information for group "([^"]*)"$/
     */
    public function iFillInCorrectGroupInformationForGroup($groupName)
    {
        $steps = $this->iFillInGroupInformationForGroupWithoutRoles($groupName);

        $roles = array(
            "ROLE_ADMIN",
            "ROLE_PERMISSIONMANAGER"
        );
        foreach($roles as $role) {
            $steps[] = new Step\When("I additionally select \"$role\" from \"group[rolesCollection][]\"");
        }

        return $steps;
    }

    /**
     * @Given /^I fill in group information for group "([^"]*)" without roles$/
     */
    public function iFillInGroupInformationForGroupWithoutRoles($groupName)
    {
        $steps = array();

        $records = array(
            "group[name]" => $this->getMainContext()->fixStepArgument($groupName)
        );
        foreach($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        return $steps;
    }

    /**
     * @Given /^I edit group "([^"]*)"$/
     */
    public function iEditGroup($groupName)
    {
        $this->getMainContext()->clickAction($groupName, 'edit', 'groups');
    }

    /**
     * @Given /^I delete group "([^"]*)"$/
     */
    public function iDeleteGroup($groupName)
    {
        $this->getMainContext()->clickAction($groupName, 'delete', 'groups');

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@class, 'modal')]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->getMainContext()->iWaitSeconds(1);

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

}
