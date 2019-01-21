<?php

{% if not isV4 %}
namespace {{ namespace }}\Features\Context;

{% endif %}
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * MediaContext
 *
 * Provides the context for the AdminMedia.feature
 */
class MediaContext implements Context
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
     * @param string $fileType the type of the file - image, pdf
     *
     * @Given /^I fill in correct file information for (.*)$/
     *
     * @return array
     */
    public function iFillInCorrectFileInformationFor($fileType)
    {
        $fileTypes = array(
            "image" => "image.png",
            "pdf" => "pdf.pdf"
        );

        $records = array(
            "kunstmaan_mediabundle_filetype[name]" => $this->mainContext->fixStepArgument($fileType)
        );
        foreach ($records as $field => $value) {
            $this->mainContext->fillField($field, $value);
        }

        $this->mainContext->attachFileToField('kunstmaan_mediabundle_filetype[file]', $fileTypes[$fileType]);
    }

    /**
     * @Given /^I delete image$/
     */
    public function iDeleteImage()
    {
        $this->mainContext->iAmOnASpecificPage("image");
        $this->performDelete();
    }

    /**
     * @Given /^I delete file$/
     */
    public function iDeleteFile()
    {
        $this->mainContext->iAmOnASpecificPage("file");
        $this->performDelete();
    }

    /**
     * @param string $slideType the type - slideshare
     * @param string $slideName the name of the slides
     *
     * @Given /^I fill in correct (.*) information for slide "([^"]*)"$/
     *
     * @return array
     */
    public function iFillInCorrectInformationForSlide($slideType, $slideName)
    {
        $slideCodes = array(
            "slideshare" => "13842545"
        );

        $records = array(
            "kunstmaan_mediabundle_slidetype[name]" => $this->mainContext->fixStepArgument($slideName),
            "kunstmaan_mediabundle_slidetype[code]" => $slideCodes[$slideType]
        );
        foreach ($records as $field => $value) {
            $this->mainContext->fillField($field, $value);
        }

        $this->mainContext->selectOption('kunstmaan_mediabundle_slidetype[type]', $slideType);
    }

    /**
     * @Given /^I delete slide$/
     */
    public function iDeleteSlide()
    {
        $this->mainContext->iAmOnASpecificPage("slide");
        $this->performDelete();
    }

    /**
     * @param string $videoType the type of the video - youtube, vimeo, dailymotion
     * @param string $videoName the name of the video
     *
     * @Given /^I fill in correct (.*) information for video "([^"]*)"$/
     *
     * @return array
     */
    public function iFillInCorrectInformationForVideo($videoType, $videoName)
    {
        $videoCodes = array(
            "youtube" => "cfSaztUiw5s",
            "vimeo" => "54269169",
            "dailymotion" => "xr8509_raw-video-spacex-dragon-leaves-space-station_tech"
        );

        $records = array(
            "kunstmaan_mediabundle_videotype[name]" => $this->mainContext->fixStepArgument($videoName),
            "kunstmaan_mediabundle_videotype[code]" => $videoCodes[$videoType]
        );
        foreach ($records as $field => $value) {
            $this->mainContext->fillField($field, $value);
        }

        $this->mainContext->selectOption('kunstmaan_mediabundle_videotype[type]', $videoType);
    }

    /**
     * @Given /^I delete video$/
     */
    public function iDeleteVideo()
    {
        $this->mainContext->iAmOnASpecificPage("video");
        $this->performDelete();
    }

    /**
     * @param string $folderName
     *
     * @Given /^I create subfolder "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iCreateSubFolder($folderName)
    {
        $records = array(
            "kunstmaan_mediabundle_FolderType[name]" => $this->mainContext->fixStepArgument($folderName),
            "kunstmaan_mediabundle_FolderType[rel]" => "media",
            "kunstmaan_mediabundle_FolderType[parent]" => "1"
        );

        $this->mainContext->pressButton("Add subfolder");
        //Wait 1 second so the modal is completely visible
        $this->mainContext->iWaitSeconds(1);

        $page = $this->mainContext->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'addsub-modal')]");

        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
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
    }

    /**
     * @param string $folderName
     *
     * @Given /^I delete subfolder "([^"]*)"$/
     */
    public function iDeleteSubFolder($folderName)
    {
        $this->performFolderDelete($folderName);
    }

    /**
     * @Given /^I delete the folder$/
     */
    public function iDeleteTheFolder()
    {
        $this->performFolderDelete();
    }

    /**
     * @param string $folderName
     *
     */
    private function performFolderDelete($folderName = "")
    {
        if ($folderName != "") {
            //Navigate to the folder we want to delete
            $this->mainContext->clickLink($folderName);
        }

        $this->mainContext->pressButton("Delete this folder");

        $page = $this->mainContext->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'delete-modal')]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->mainContext->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $this->mainContext->findAndClickButton($page, 'xpath', "//a[contains(@href, 'del')]");

                return;
            }
        }
    }

    private function performDelete()
    {
        $page = $this->mainContext->getSession()->getPage();
        $this->mainContext->findAndClickButton($page, 'xpath', "//button[contains(@class, 'del')]");

        $modals = $page->findAll('xpath', "//div[contains(@id, 'sure-modal')]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->mainContext->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $this->mainContext->findAndClickButton($page, 'xpath', "//a[contains(@href, 'del')]");

                return;
            }
        }
    }
}
