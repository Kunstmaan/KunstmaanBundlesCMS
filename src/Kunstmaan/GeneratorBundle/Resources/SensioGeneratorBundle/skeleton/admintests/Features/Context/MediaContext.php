<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

class MediaContext extends BehatContext
{

    /**
     * @Given /^I fill in correct file information for (.*)$/
     */
    public function iFillInCorrectFileInformationFor($fileType)
    {
        $fileTypes = array(
            "image" => "image.jpg",
            "pdf" => "pdf.pdf"
        );

        $steps = array();

        $records = array(
            "kunstmaan_mediabundle_filetype[name]" => $this->getMainContext()->fixStepArgument($fileType)
        );
        foreach($records as $field => $value) {
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
     * @Given /^I fill in correct (.*) information for slide "([^"]*)"$/
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
        foreach($records as $field => $value) {
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
     * @Given /^I fill in correct (.*) information for video "([^"]*)"$/
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
        foreach($records as $field => $value) {
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
     * @Given /^I create subfolder "([^"]*)"$/
     */
    public function iCreateSubFolder($folderName)
    {
        $records = array(
            "kunstmaan_mediabundle_FolderType[name]" => $this->getMainContext()->fixStepArgument($folderName),
            "kunstmaan_mediabundle_FolderType[rel]" => "media",
            "kunstmaan_mediabundle_FolderType[parent]" => "Media"
        );

        $this->getMainContext()->pressButton("Add subfolder");

        $page = $this->getMainContext()->getSession()->getPage();
        $modals = $page->findAll('xpath', "//div[contains(@id, 'addsub-modal')]");

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
     * @Given /^I delete subfolder "([^"]*)"$/
     */
    public function iDeleteSubFolder($folderName)
    {
        //Navigate to the folder we want to delete
        $this->getMainContext()->clickLink($folderName);

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
                $deleteLink = $modal->find('xpath', '//a[text()="Delete"]');
                $deleteLink->click();

                return;
            }
        }
    }

    private function performDelete()
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $deleteButton = $page->find('xpath', "//a[contains(@class, 'del')]");
        $deleteButton->click();
    }
}
