<?php

namespace Kunstmaan\NodeBundle\Tests\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Helper\URLHelper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Routing\RouterInterface;

class UrlHelperTest extends TestCase
{
    /** @var Connection */
    private $connection;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->createSchema();

        $this->connection->transactional(
            static function (Connection $connection): void {
                for ($i = 1; $i <= 5; ++$i) {
                    $connection->insert('kuma_node_translations', ['url' => 'abc-' . $i, 'lang' => 'en']);
                    $connection->insert('kuma_media', ['url' => '/uploads/media/' . $i . '/test.svg']);
                }
            }
        );
    }

    public function testReplaceUrlWithEmail()
    {
        $em = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();

        $urlHelper = new URLHelper($em, $router, $logger, $domainConfig);

        $this->assertEquals('mailto:test@example.com', $urlHelper->replaceUrl('test@example.com'));
    }

    public function testReplaceUrlWithInternalLink()
    {
        $em = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('getConnection')->willReturn($this->connection);
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $router->method('generate')->with('_slug', ['url' => 'abc-3'])->willReturn('/abc-3');
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();

        $urlHelper = new URLHelper($em, $router, new NullLogger(), $domainConfig);
        $this->assertEquals('/abc-3', $urlHelper->replaceUrl('[NT3]'));

        // Remove all records to test cached result on second call
        $this->connection->executeStatement('DELETE FROM kuma_node_translations');

        // Second call to replaceUrl should not execute query again
        $this->assertEquals('/abc-3', $urlHelper->replaceUrl('[NT3]'));
    }

    public function testReplaceUrlWithMediaLink()
    {
        $em = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('getConnection')->willReturn($this->connection);
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $router->method('generate')->with('_slug', ['url' => 'abc'])->willReturn('/abc');
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();

        $urlHelper = new URLHelper($em, $router, new NullLogger(), $domainConfig);
        $this->assertEquals('/uploads/media/3/test.svg', $urlHelper->replaceUrl('[M3]'));

        // Remove all records to test cached result on second call
        $this->connection->executeStatement('DELETE FROM kuma_media');

        // Second call to replaceUrl should not execute query again
        $this->assertEquals('/uploads/media/3/test.svg', $urlHelper->replaceUrl('[M3]'));
    }

    private function createSchema(): void
    {
        $schema = new Schema();
        $nt = $schema->createTable('kuma_node_translations');
        $nt->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $nt->addColumn('url', 'string', ['length' => 255]);
        $nt->addColumn('lang', 'string', ['length' => 4]);
        $nt->setPrimaryKey(['id']);

        $nt = $schema->createTable('kuma_media');
        $nt->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $nt->addColumn('url', 'string', ['length' => 255]);
        $nt->setPrimaryKey(['id']);

        $queries = $schema->toSql($this->connection->getDatabasePlatform());

        foreach ($queries as $sql) {
            $this->connection->executeQuery($sql);
        }
    }
}
