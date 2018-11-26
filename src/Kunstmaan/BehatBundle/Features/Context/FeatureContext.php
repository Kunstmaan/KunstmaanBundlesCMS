<?php

namespace Kunstmaan\BehatBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\AfterTestScope;

class FeatureContext extends MinkContext implements Context
{
    protected $browserName;

    public function __construct(array $parameters)
    {
        $this->browserName = $this->getMinkParameter('browser_name');
    }

    /**
     * Override method to wait for Ajax requests to finish before continuing
     *
     * @param $text
     */
    public function assertPageContainsText($text)
    {
        $this->getSession()->wait(10000, '(typeof(jQuery)=="undefined" || (0 === jQuery.active && 0 === jQuery(\':animated\').length))');
        parent::assertPageContainsText($text);
    }

    /**
     * Checks that form hidden field with specified id|name has specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" hidden field should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function hiddenFieldValueEquals($field, $value)
    {
        $node = $this->findHiddenField($field);
        $actual = $node->getValue();
        $regex = '/^' . preg_quote($value, '/') . '/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The hidden field "%s" value is "%s", but "%s" expected.', $field, $actual, $value);

            throw new ExpectationException($message, $this->getSession());
        }
    }

    /**
     * Checks that form hidden field with specified id|name has specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" hidden field should not contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function hiddenFieldValueNotEquals($field, $value)
    {
        $node = $this->findHiddenField($field);
        $actual = $node->getValue();
        $regex = '/^' . preg_quote($value, '/') . '/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The hidden field "%s" value is "%s", but it should not be.', $field, $actual);

            throw new ExpectationException($message, $this->getSession());
        }
    }

    /**
     * @param string $field
     *
     * @return \Behat\Mink\Element\NodeElement|null
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    private function findHiddenField($field)
    {
        $node = $this->getSession()->getPage()->find(
            'xpath',
            strtr(
                ".//input[./@type = 'hidden'][(./@id = '%locator%' or ./@name = '%locator%')]",
                array('%locator%' => $field)
            )
        );
        if (null === $node) {
            throw new ElementNotFoundException($this->getSession(), 'hidden field', 'id|name', $field);
        }

        return $node;
    }

    /**
     * Take screenshot when step fails.
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(AfterTestScope $event)
    {
        if (4 === $event->getTestResult()) {
            $driver = $this->getSession()->getDriver();
            if (!($driver instanceof Selenium2Driver)) {
                throw new UnsupportedDriverActionException('Taking screenshots is not supported by %s, use Selenium2Driver instead.', $driver);
            }
            $directory = 'build/behat/' . $event->getLogicalParent()->getFeature()->getTitle();
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $filename = sprintf('%s_%s_%s_%s.%s', $event->getLogicalParent()->getTitle(), $this->browserName, date('YmdHis'), uniqid('', true), 'png');
            file_put_contents($directory . '/' . $filename, $driver->getScreenshot());
        }
    }
}
