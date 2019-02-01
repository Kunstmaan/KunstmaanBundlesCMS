<?php

namespace Tests\Kunstmaan\UtilitiesBundle\Helper;

use Kunstmaan\UtilitiesBundle\Helper\UrlTransactionNamingStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UrlTransactionNamingStrategyTest
 */
class UrlTransactionNamingStrategyTest extends TestCase
{
    public function testGetTransactionName()
    {
        $request = new Request();
        $strategy = new UrlTransactionNamingStrategy();
        $name = $strategy->getTransactionName($request);
        $this->assertEquals('/', $name);
    }
}
