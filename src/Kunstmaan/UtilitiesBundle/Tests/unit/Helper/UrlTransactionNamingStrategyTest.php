<?php

namespace Tests\Kunstmaan\UtilitiesBundle\Helper;

use Kunstmaan\UtilitiesBundle\Helper\UrlTransactionNamingStrategy;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UrlTransactionNamingStrategyTest
 */
class UrlTransactionNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTransactionName()
    {
        $request = new Request();
        $strategy = new UrlTransactionNamingStrategy();
        $name = $strategy->getTransactionName($request);
        $this->assertEquals('/', $name);
    }
}
