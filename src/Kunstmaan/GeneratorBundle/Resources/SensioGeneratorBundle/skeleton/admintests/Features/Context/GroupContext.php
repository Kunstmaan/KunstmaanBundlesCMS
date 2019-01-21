<?php

{% if not isV4 %}
namespace {{ namespace }}\Features\Context;

{% endif %}
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * GroupContext
 *
 * Provides the context for the AdminSettingsGroup.feature
 */
class GroupContext implements Context
{
    /** @var FeatureContext $mainContext */
    private $mainContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->mainContext = $environment->getContext(FeatureContext::class);
    }

    /**
     * @param string $groupName
     *
     * @Given /^I fill in correct group information for group "([^"]*)"$/
     *
     * @return array
     */
    public function iFillInCorrectGroupInformationForGroup($groupName)
    {
        $this->iFillInGroupInformationForGroupWithoutRoles($groupName);

        $roles = array(
            "ROLE_ADMIN",
            "ROLE_PERMISSIONMANAGER"
        );
        foreach ($roles as $role) {
            $this->mainContext->additionallySelectOption('group[rolesCollection][]', $role);
        }
    }

    /**
     * @param string $groupName
     *
     * @Given /^I fill in group information for group "([^"]*)" without roles$/
     *
     * @return array
     */
    public function iFillInGroupInformationForGroupWithoutRoles($groupName)
    {
        $records = array(
            "group[name]" => $this->mainContext->fixStepArgument($groupName)
        );

        foreach ($records as $field => $value) {
            $this->mainContext->fillField($field, $value);
        }
    }

    /**
     * @param string $groupName
     *
     * @Given /^I edit group "([^"]*)"$/
     */
    public function iEditGroup($groupName)
    {
        $this->mainContext->clickAction($groupName, 'Edit', 'groups');
    }

    /**
     * @param string $groupName
     *
     * @Given /^I delete group "([^"]*)"$/
     */
    public function iDeleteGroup($groupName)
    {
        $this->mainContext->clickAction($groupName, 'Delete', 'groups');

        $page = $this->mainContext->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@class, 'modal')]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->mainContext->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $this->mainContext->findAndClickButton($modal, 'xpath', "//form//button[@type='submit']");

                return;
            }
        }
    }

}
