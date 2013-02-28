<?php

namespace Kunstmaan\BehatBundle\Context\SubContext;

use Behat\Behat\Context\BehatContext;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * SubContext which creates a screenshot when a step fails.
 * Screenshots will be saved to the "build/behat" folder.
 */
class FailedScreenshotSubContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters
     */
    public function __construct(array $parameters)
    {

    }

    /**
     * Get Mink session from MinkContext
     */
    public function getSession($name = null)
    {
        return $this->getMainContext()->getSession($name);
    }

    /**
     * Take screenshot when step fails.
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep($event)
    {
        if (4 === $event->getResult()) {
            $driver = $this->getSession()->getDriver();
            if (!($driver instanceof Selenium2Driver)) {
                throw new UnsupportedDriverActionException('Taking screenshots is not supported by %s, use Selenium2Driver instead.', $driver);
                return;
            }
            $directory = 'build/behat/'.$event->getLogicalParent()->getFeature()->getTitle().'.'.$event->getLogicalParent()->getTitle();
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $filename = sprintf('%s_%s_%s.%s', $this->getMinkParameter('browser_name'), date('c'), uniqid('', true), 'png');
            file_put_contents($directory.'/'.$filename, $driver->getScreenshot());
        }
    }
}
