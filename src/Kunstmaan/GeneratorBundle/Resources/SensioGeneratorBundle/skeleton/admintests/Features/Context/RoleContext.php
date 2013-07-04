<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

class RoleContext extends BehatContext
{

    /**
     * @Given /^I fill in correct role information for role "([^"]*)"$/
     */
    public function iFillInCorrectRoleInformationForRole($roleName)
    {
        $steps = array();

        $records = array(
            "role[role]" => $this->getMainContext()->fixStepArgument($roleName)
        );
        foreach($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        return $steps;
    }

    /**
     * @Given /^I edit role "([^"]*)"$/
     */
    public function iEditRole($roleName)
    {
        $this->getMainContext()->clickAction($roleName, 'edit', 'roles');
    }

    /**
     * @Given /^I delete role "([^"]*)"$/
     */
    public function iDeleteRole($roleName)
    {
        $this->getMainContext()->clickAction($roleName, 'delete', 'roles');

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
