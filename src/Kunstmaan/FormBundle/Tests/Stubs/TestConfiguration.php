<?php

namespace Kunstmaan\FormBundle\Tests\Stubs;

use Doctrine\ORM\Configuration;

/**
 * TestConfiguration
 */
class TestConfiguration extends Configuration
{
    /**
     * @return \Doctrine\ORM\Doctrine\ORM\Mapping\QuoteStrategy|null
     */
    public function getQuoteStrategy()
    {
        return null;
    }
}
