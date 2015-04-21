<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * MediaContext
 *
 * Provides the context for the AdminMedia.feature
 */
class MediaContext extends BehatContext
{

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

        $steps = array();

        $records = array(
            "kunstmaan_mediabundle_filetype[name]" => $this->getMainContext()->fixStepArgument($fileType)
        );
        foreach ($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        $steps[] = new Step\When("I attach the file \"$fileTypes[$fileType]\" to \"kunstmaan_mediabundle_filetype[file]\"");

        return $steps;
    }

    /**
     * @Given /^I delete image$/
     */
    public function iDeleteImage()
    {
        $this->getMainContext()->iAmOnASpecificPage("image");
        $this->performDelete();
    }

    /**
     * @Given /^I delete file$/
     */
    public function iDeleteFile()
    {
        $this->getMainContext()->iAmOnASpecificPage("file");
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

        $steps = array();

        $records = array(
            "kunstmaan_mediabundle_slidetype[name]" => $this->getMainContext()->fixStepArgument($slideName),
            "kunstmaan_mediabundle_slidetype[code]" => $slideCodes[$slideType]
        );
        foreach ($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        $steps[] = new Step\When("I select \"$slideType\" from \"kunstmaan_mediabundle_slidetype[type]\"");

        return $steps;
    }

    /**
     * @Given /^I delete slide$/
     */
    public function iDeleteSlide()
    {
        $this->getMainContext()->iAmOnASpecificPage("slide");
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

        $steps = array();

        $records = array(
            "kunstmaan_mediabundle_videotype[name]" => $this->getMainContext()->fixStepArgument($videoName),
            "kunstmaan_mediabundle_videotype[code]" => $videoCodes[$videoType]
        );
        foreach ($records as $field => $value) {
            $steps[] = new Step\When("I fill in \"$field\" with \"$value\"");
        }

        $steps[] = new Step\When("I select \"$videoType\" from \"kunstmaan_mediabundle_videotype[type]\"");

        return $steps;
    }

    /**
     * @Given /^I delete video$/
     */
    public function iDeleteVideo()
    {
        $this->getMainContext()->iAmOnASpecificPage("video");
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
            "kunstmaan_mediabundle_FolderType[name]" => $this->getMainContext()->fixStepArgument($folderName),
            "kunstmaan_mediabundle_FolderType[rel]" => "media",
            "kunstmaan_mediabundle_FolderType[parent]" => "Media"
        );

        $this->getMainContext()->pressButton("Add subfolder");
        //Wait 1 second so the modal is completely visible
        $this->getMainContext()->iWaitSeconds(1);

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'addsub-modal')]");

        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                foreach ($records as $field => $value) {
                    $modalField = $modal->findField($field);
                    if (null === $modalField) {
                        throw new ElementNotFoundException(
                            $this->getSession(), 'form field', 'id|name|label|value', $field
                        );
                    }
                    $modalField->setValue($value);
                }
                $this->getMainContext()->findAndClickButton($modal, 'xpath', "//form//button[@type='submit']");

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
            $this->getMainContext()->clickLink($folderName);
        }

        $this->getMainContext()->pressButton("Delete this folder");

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'delete-modal')]");

        //Wait 1 second for the modal to be visible
        //Else we can get a error when running the tests.
        $this->getMainContext()->iWaitSeconds(1);

        // Find the visible modal.
        // Couldn't do this via xpath using : [contains(@class, 'modal') and contains(@class, 'in')]
        foreach ($modals as $modal) {
            if ($modal->hasClass('in')) {
                $this->getMainContext()->findAndClickButton($page, 'xpath', "//a[contains(@href, 'del')]");

                return;
            }
        }
    }

    private function performDelete()
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $this->getMainContext()->findAndClickButton($page, 'xpath', "//a[contains(@class, 'del')]");
    }
}
