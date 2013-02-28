<?php

namespace Kunstmaan\BehatBundle\Features\Context;

use Behat\MinkExtension\Context\MinkContext;
use Kunstmaan\BehatBundle\Context\SubContext\RadioButtonSubContext;
use Kunstmaan\BehatBundle\Context\SubContext\FailedScreenshotSubContext;
use Behat\Mink\Driver\Selenium2Driver;

class FeatureContext extends MinkContext
{

    public function __construct(array $parameters)
    {
        $this->useContext('FailedScreenshotSubContext', new FailedScreenshotSubContext($parameters));
        $this->useContext('RadioButtonSubContext', new RadioButtonSubContext($parameters));
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


}
