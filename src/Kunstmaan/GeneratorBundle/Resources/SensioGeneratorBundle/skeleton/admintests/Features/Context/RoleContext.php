<?php

{% if not isV4 %}
namespace {{ namespace }}\Features\Context;

{% endif %}
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * RoleContext
 *
 * Provides the context for the AdminSettingsRole.feature
 */
class RoleContext implements Context
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
     * @param string $roleName
     *
     * @Given /^I fill in correct role information for role "([^"]*)"$/
     *
     * @return array
     */
    public function iFillInCorrectRoleInformationForRole($roleName)
    {
        $steps = array();

        $records = array(
            "role[role]" => $this->mainContext->fixStepArgument($roleName)
        );
        foreach ($records as $field => $value) {
            $this->mainContext->fillField($field, $value);
        }

        return $steps;
    }

    /**
     * @param string $roleName
     *
     * @Given /^I edit role "([^"]*)"$/
     */
    public function iEditRole($roleName)
    {
        $this->mainContext->clickAction($roleName, 'Edit', 'roles');
    }

    /**
     * @param string $roleName
     *
     * @Given /^I delete role "([^"]*)"$/
     */
    public function iDeleteRole($roleName)
    {
        $this->mainContext->clickAction($roleName, 'Delete', 'roles');

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
