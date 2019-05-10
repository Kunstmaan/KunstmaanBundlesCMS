<?php

{% if not isV4 %}
namespace {{ namespace }}\Features\Context;

{% endif %}
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * UserContext
 *
 * Provides the context for the AdminSettingsUser.feature
 */
class UserContext implements Context
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
     * @param string $username
     *
     * @Given /^I fill in correct user information for username "([^\']*)"$/
     *
     * @return array
     */
    public function iFillInCorrectUserInformation($username)
    {
        $username = $this->mainContext->fixStepArgument($username);
        $password = $this->mainContext->getPasswordForUsername($username);

        $records = array(
            "user[username]" => $username,
            "user[plainPassword][first]" => $password,
            "user[plainPassword][second]" => $password,
            "user[email]" => "support+" . $username . "@kunstmaan.be",
        );

        foreach ($records as $field => $value) {
            $this->mainContext->fillField($field, $value);
        }

        $this->mainContext->checkOption("user[enabled]");
        $this->mainContext->selectOption('user[groups][]', 'Administrators');
    }

    /**
     * @param string $username
     *
     * @Given /^I edit user "([^"]*)"$/
     */
    public function iEditUser($username)
    {
        $this->mainContext->clickAction($username, 'Edit', 'users');
    }

    /**
     * @param string $username
     *
     * @Given /^I delete user "([^"]*)"$/
     */
    public function iDeleteUser($username)
    {
        $this->mainContext->clickAction($username, 'Delete', 'users');

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
