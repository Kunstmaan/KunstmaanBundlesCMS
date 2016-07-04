<?php

namespace {{ namespace }}\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * PagePartContext
 *
 * Provides the context for the AdminPagePart.feature
 */
class PagePartContext extends BehatContext
{
    /**
     * @param string $context The context
     * @param string $pagePartName The name of the pagepart
     *
     * @Given /^I add pp "([^"]*)" in section "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iAddPagePart($pagePartName, $context)
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $element = $page->find('xpath', "//div[contains(@data-context, '" . $context . "')]//select");
        $this->selectOption($element, $pagePartName);
        $this->getMainContext()->iWaitSeconds(2);
    }

    /**
     * @param string $name The name
     * @param string $value The value
     *
     * @Given /^I fill in pp cke field "([^"]*)" with "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iFillPagePartCkeEditor($name, $value)
    {
        $this->getMainContext()->getSession()->switchToIFrame('cke_iframe_' . lcfirst($name));
        $this->getMainContext()->getSession()->executeScript("document.body.innerHTML='$value'");
        $this->getMainContext()->getSession()->switchToIFrame();
    }

    /**
     * @param string $name The name
     * @param string $value The value
     *
     * @Given /^I fill in pp image field "([^"]*)" with "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iFillPagePartImage($name, $value)
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $field = $page->find('xpath', "//label[contains(normalize-space(string(.)), '$name')]");
        $element = $field->getParent()->find('xpath', "//input");
        $name = $element->getAttribute('name');
        $javascript = "document.getElementsByName('" . $name . "')[0].value='" . $value . "';";
        $this->getMainContext()->getSession()->executeScript($javascript);
    }

    /**
     * @param string $name The pagepart name
     *
     * @Given /^I edit pagepart "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iClickEditPagePart($name)
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $field = $page->find('xpath', "//h6[contains(normalize-space(string(.)), '$name')]");
        $button = $field->getParent()->find('xpath', "//button[contains(@class, 'edit')]");
        $button->click();
    }

    /**
     * @param string $name The pagepart name
     *
     * @Given /^I delete pagepart "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iClickDeletePagePart($name)
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $field = $page->find('xpath', "//h6[contains(normalize-space(string(.)), '$name')]");
        $button = $field->getParent()->find('xpath', "//button[contains(@class, 'del')]");
        $button->click();

        $this->getMainContext()->iWaitSeconds(2);

        $field = $page->find('xpath', "//h3[contains(normalize-space(string(.)), \"Delete pagepart '$name'\")]");
        $button = $field->getParent()->getParent()->find('xpath', "//button[contains(normalize-space(string(.)), 'Delete')]");
        $button->click();

        $this->getMainContext()->iWaitSeconds(1);
    }

    /**
     * @param string $name The name
     * @param string $dateValue The date value (dd/mm/yyyy)
     * @param string $timeValue The time value (hh:ss)
     *
     * @Given /^I fill in pp datetime field "([^"]*)" with "([^"]*)" "([^"]*)"$/
     *
     * @throws ElementNotFoundException
     */
    public function iFillPagePartDateTime($name, $dateValue, $timeValue)
    {
        $page = $this->getMainContext()->getSession()->getPage();
        $field = $page->find('xpath', "//label[contains(normalize-space(string(.)), '$name')]");
        $element = $field->getParent()->find('xpath', "//input[contains(@class, 'form_datepicker')]");
        $element->setValue($dateValue);
        $element = $field->getParent()->find('xpath', "//input[contains(@class, 'form_timepicker')]");
        $element->setValue($timeValue);

        if ($element->getValue() == '') {
            $id = $element->getAttribute('id');
            if (!empty($id)) {
                $javascript = "document.getElementById('" . $id . "').value='" . $timeValue . "';";
                $this->getMainContext()->getSession()->executeScript($javascript);
            }
        }
    }

    /**
     * Selects current node specified option if it's a select field.
     *
     * @param NodeElement $element
     * @param string  $option
     * @param Boolean $multiple
     *
     * @throws ElementNotFoundException
     */
    public function selectOption($element, $option, $multiple = false)
    {
        if ('select' !== $element->getTagName()) {
            $element->getSession()->getDriver()->selectOption($element->getXpath(), $option, $multiple);

            return;
        }

        $opt = $element->find('named', array(
            'option', $element->getSession()->getSelectorsHandler()->xpathLiteral($option)
        ));

        if (null === $opt) {
            throw new ElementNotFoundException($element->getSession(), 'select option', 'value|text', $option);
        }

        $element->getSession()->getDriver()->selectOption(
            $element->getXpath(),
            str_replace("\\", "\\\\", $opt->getValue()),
            $multiple
        );
    }
}
