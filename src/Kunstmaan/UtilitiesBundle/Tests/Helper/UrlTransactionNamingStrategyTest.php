<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper;

use Kunstmaan\UtilitiesBundle\Helper\UrlTransactionNamingStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

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
