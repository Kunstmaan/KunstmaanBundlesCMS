<?php

{% if not isV4 %}
namespace {{ namespace }}\Features\Context;

{% endif %}
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use {{ namespace }}\Entity\Pages\ContentPage;
use {{ namespace }}\Entity\Pages\FormPage;

/**
 * PageContext
 *
 * Provides the context for the AdminPage.feature
 */
class PageContext implements Context
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
     * @param string $pageName
     *
     * @Given /^I preview the (.*) page$/
     */
    public function iPreviewPage($pageName)
    {
        $this->mainContext->iAmOnASpecificPage($pageName);
        $this->mainContext->clickLink("Preview");
    }

    /**
     * @param string $pageType The type of the page - ContentPage, FormPage
     * @param string $pageName The name of the page
     *
     * @Given /^I add ([a-zA-Z0-9]*) "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iAddPage($pageType, $pageName)
    {
        switch ($pageType) {
            case 'ContentPage':
                $pageType = ContentPage::class;
                break;
            case 'FormPage':
                $pageType = FormPage::class;
                break;
        }
        $records = array(
            "addpage_title" => $this->mainContext->fixStepArgument($pageName),
            "addpage_type" => $this->mainContext->fixStepArgument($pageType)
        );

        $this->mainContext->pressButton("More");
        $this->mainContext->pressButton("Add subpage");
        $this->mainContext->iWaitSeconds(1);

        $page = $this->mainContext->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'add-subpage-modal')]");

        foreach ($modals as $modal) {
            foreach ($records as $field => $value) {
                $modalField = $modal->findField($field);
                if (null === $modalField) {
                    throw new ElementNotFoundException($this->mainContext->getSession(), 'form field', 'id|name|label|value', $field);
                }
                $modalField->setValue($value);
            }
            $this->mainContext->findAndClickButton($modal, 'xpath', "//form//button[@type='submit']");

            return;
        }
    }

    /**
     * @Given /^I save the current page$/
     */
    public function iSaveCurrentPage()
    {
        $this->mainContext->iScrollToTop();
        $this->mainContext->pressButton("Save");
    }

    /**
     * @param string $pageName
     *
     * @Given /^I save page "([^"]*)"$/
     */
    public function iSavePage($pageName)
    {
        $this->save($pageName, "Save");
    }

    /**
     * @param string $pageName
     *
     * @Given /^I save page "([^"]*)" as draft$/
     */
    public function iSavePageAsDraft($pageName)
    {
        $this->save($pageName, "Save as draft");
    }

    /**
     * @param string $pageName The name of the page
     * @param string $action The action that needs to be performed - Save, Save as draft
     */
    private function save($pageName, $action)
    {
        // Navigate to the admin home page to see the tree
        $this->mainContext->iAmOnASpecificPage("admin home");
        // Navigate to the page we want to publish
        $this->mainContext->clickLink($pageName);
        // Click the save button
        $this->mainContext->pressButton("More");
        $this->mainContext->pressButton($action);
    }

    /**
     * @param string $pageName
     *
     * @Given /^I go to admin page "([^"]*)"$/
     */
    public function iGoToAdminPage($pageName)
    {
        // Navigate to the admin home page to see the tree
        $this->mainContext->iAmOnASpecificPage("admin home");
        $this->iClickAdminPage($pageName);
    }

    /**
     * @param string $pageName
     *
     * @Given /^I click on admin page "([^"]*)"$/
     */
    public function iClickAdminPage($pageName)
    {
        // Navigate to the page we want to publish
        $this->mainContext->clickLink($pageName);
    }

    /**
     * @param string $pageName
     *
     * @Given /^I publish page "([^"]*)"$/
     */
    public function iPublishPage($pageName)
    {
        $this->placePageInState($pageName, 'Publish');
    }

    /**
     * @Given /^I publish the current page$/
     */
    public function iPublishCurrentPage()
    {
        $this->placePageInState(null, 'Publish');
    }

    /**
     * @Given /^I unpublish the current page$/
     */
    public function iUnPublishCurrentPage()
    {
        $this->placePageInState(null, 'Unpublish');
    }

    /**
     * @Given /^I unpublish page "([^"]*)"$/
     */
    public function iUnPublishPage($pageName)
    {
        $this->placePageInState($pageName, 'Unpublish');
    }

    /**
     * @param string $pageName The name of the page, if null the current page
     * @param string $state The state of the page - Publish, Unpublish
     *
     * @throws ExpectationException
     */
    private function placePageInState($pageName, $state)
    {
        $states = array(
            'Publish' => 'pub',
            'Unpublish' => 'unpub'
        );

        if (!is_null($pageName)) {
            // Navigate to the admin home page to see the tree
            $this->mainContext->iAmOnASpecificPage("admin home");
            // Navigate to the page we want to publish
            $this->mainContext->clickLink($pageName);
        } else {
            $this->mainContext->iScrollToTop();
        }

        $page = $this->mainContext->getSession()->getPage();

        $this->mainContext->pressButton("More");
        $this->mainContext->clickLink($state);

        $modals = $page->findAll('xpath', "//div[contains(@id, $states[$state])]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->mainContext->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $this->mainContext->findAndClickButton($modal, 'xpath', "//button[contains(@class, 'btn btn-danger btn--raise-on-hover')]");

                return;
            }
        }
    }

    /**
     * @param string $pageName
     *
     * @Given /^I delete page "([^"]*)"$/
     */
    public function iDeletePage($pageName)
    {
        // Navigate to the admin home page to see the tree
        $this->mainContext->iAmOnASpecificPage("admin home");
        // Navigate to the page we want to delete
        $this->mainContext->clickLink($pageName);

        $this->mainContext->pressButton("More");
        $this->mainContext->pressButton("Delete");

        $page = $this->mainContext->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'delete-page-modal')]");

        // Wait 1 second for the modal to be visible
        // Else we can get a error when running the tests.
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

    /**
     * @param string $pageTemplateName
     *
     * @Given /^I change page template "([^"]*)"$/
     * @throws ElementNotFoundException
     */
    public function iChangePageTemplate($pageTemplateName)
    {
        $this->mainContext->iScrollToBottom();
        $this->mainContext->pressButton("change-template-button");

        // Wait 1 second for the modal to be visible
        // Else we can get a error when running the tests.
        $this->mainContext->iWaitSeconds(1);

        $page = $this->mainContext->getSession()->getPage();
        $radioButton = $page->findField($pageTemplateName);
        if (null === $radioButton) {
            throw new ElementNotFoundException($this->mainContext->getSession(), 'form field', 'id|name|label|value', $pageTemplateName);
        }
        $radioButton->click();

        $this->mainContext->pressButton("change-template");
    }

}
