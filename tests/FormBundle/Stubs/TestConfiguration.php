<?php

namespace Tests\Kunstmaan\FormBundle\Stubs;

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
