<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

class PageContext extends BehatContext
{

    /**
     * @Given /^I preview the (.*) page$/
     */
    public function iPreviewPage($pageName)
    {
        $this->getMainContext()->iAmOnASpecificPage($pageName);
        $this->getMainContext()->clickLink("Preview");
    }

    /**
     * @Given /^I add (.*) "([^"]*)"$/
     */
    public function iAddPage($pageType, $pageName)
    {
        $records = array(
            "addpage_title" => $this->getMainContext()->fixStepArgument($pageName),
            "addpage_type" => $this->getMainContext()->fixStepArgument($pageType)
        );

        $this->getMainContext()->pressButton("Add subpage");

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'add-subpage-modal')]");

        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                foreach($records as $field => $value) {
                    $modalField = $modal->findField($field);
                    if (null === $modalField) {
                        throw new ElementNotFoundException(
                            $this->getSession(), 'form field', 'id|name|label|value', $field
                        );
                    }
                    $modalField->setValue($value);
                }

                $confirmButton = $modal->find('xpath', "//form//button[@type='submit']");
                $confirmButton->click();

                return;
            }
        }
    }

    /**
     * @Given /^I save page "([^"]*)"$/
     */
    public function iSavePage($pageName)
    {
        $this->save($pageName, "Save");
    }

    /**
     * @Given /^I save page "([^"]*)" as draft$/
     */
    public function iSavePageAsDraft($pageName)
    {
        $this->save($pageName, "Save as draft");
    }

    /**
     * @param $pageName The name of the page
     * @param $action   The action that needs to be performed (Save, Save as draft)
     */
    private function save($pageName, $action)
    {
        // Navigate to the admin home page to see the tree
        $this->getMainContext()->iAmOnASpecificPage("admin home");
        // Navigate to the page we want to publish
        $this->getMainContext()->clickLink($pageName);
        // Click the save button
        $this->getMainContext()->pressButton($action);
    }

    /**
     * @Given /^I publish page "([^"]*)"$/
     */
    public function iPublishPage($pageName)
    {
        $this->placePageInState($pageName, 'Publish');
    }

    /**
     * @Given /^I unpublish page "([^"]*)"$/
     */
    public function iUnPublishPage($pageName)
    {
        $this->placePageInState($pageName, 'Unpublish');
    }

    private function placePageInState($pageName, $state)
    {
        $states = array(
            'Publish' => 'pub',
            'Unpublish' => 'unpub_publish_action'
        );
        // Navigate to the admin home page to see the tree
        $this->getMainContext()->iAmOnASpecificPage("admin home");
        // Navigate to the page we want to publish
        $this->getMainContext()->clickLink($pageName);

        $page = $this->getMainContext()->getSession()->getPage();

        $publishButton = $page->find('xpath', "descendant-or-self::*[@class and contains(concat(' ', normalize-space(@class), ' '), ' main_actions ')]/descendant-or-self::*/a");
        $publishButton->click();

        $modals = $page->findAll('xpath', "//div[contains(@id, $states[$state])]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        //$this->getMainContext()->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $button = $modal->find('xpath', '//a[text()="'.$state.'"]');
                $button->click();

                return;
            }
        }
    }

    /**
     * @Given /^I delete page "([^"]*)"$/
     */
    public function iDeletePage($pageName)
    {
        // Navigate to the admin home page to see the tree
        $this->getMainContext()->iAmOnASpecificPage("admin home");
        // Navigate to the page we want to delete
        $this->getMainContext()->clickLink($pageName);

        $this->getMainContext()->pressButton("Delete");

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'delete-page-modal')]");

        // Wait 1 second for the modal to be visible
        // Else we can get a error when running the tests.
        $this->getMainContext()->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $deleteButton = $modal->find('xpath', "//form//button[@type='submit']");
                $deleteButton->click();

                return;
            }
        }
    }

}
