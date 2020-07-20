<?php

namespace Kunstmaan\NodeBundle\Tests\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Helper\URLHelper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlHelperTest extends TestCase
{
    public function testReplaceUrlWithEmail()
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();

        $urlHelper = new URLHelper($em, $router, $logger, $domainConfig);

        $this->assertEquals('mailto:test@example.com', $urlHelper->replaceUrl('test@example.com'));
    }

    public function testReplaceUrlWithInternalLink()
    {
        $stmt = $this->getMockBuilder(Statement::class)->getMock();
        $stmt->expects($this->once())->method('fetch')->willReturn(['id' => 18, 'url' => 'abc']);
        $conn = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $conn->expects($this->once())->method('executeQuery')->willReturn($stmt);
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('getConnection')->willReturn($conn);
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $router->method('generate')->with('_slug', ['url' => 'abc'])->willReturn('/abc');
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();

        $urlHelper = new URLHelper($em, $router, $logger, $domainConfig);
        $this->assertEquals('/abc', $urlHelper->replaceUrl('[NT18]'));

        //Second call to replaceUrl should not execute query again
        $this->assertEquals('/abc', $urlHelper->replaceUrl('[NT18]'));
    }

    public function testReplaceUrlWithMediaLink()
    {
        $stmt = $this->getMockBuilder(Statement::class)->getMock();
        $stmt->expects($this->once())->method('fetch')->willReturn(['id' => 18, 'url' => '/uploads/media/5e24be27412e6/test.svg']);
        $conn = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $conn->expects($this->once())->method('executeQuery')->willReturn($stmt);
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('getConnection')->willReturn($conn);
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $router->method('generate')->with('_slug', ['url' => 'abc'])->willReturn('/abc');
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();

        $urlHelper = new URLHelper($em, $router, $logger, $domainConfig);
        $this->assertEquals('/uploads/media/5e24be27412e6/test.svg', $urlHelper->replaceUrl('[M18]'));

        //Second call to replaceUrl should not execute query again
        $this->assertEquals('/uploads/media/5e24be27412e6/test.svg', $urlHelper->replaceUrl('[M18]'));
    }
}
