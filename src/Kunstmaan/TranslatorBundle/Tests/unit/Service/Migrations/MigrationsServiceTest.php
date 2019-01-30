<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Migrations;

use Kunstmaan\TranslatorBundle\Service\Migrations\MigrationsService;
use Kunstmaan\TranslatorBundle\Tests\unit\WebTestCase;

class MigrationsServiceTest extends WebTestCase
{
    private $migrationsService;

    public function setUp()
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        /* @var MigrationsService migrationsService */
        $this->migrationsService = $container->get('kunstmaan_translator.service.migrations.migrations');
    }

    /**
     * @group migrations
     */
    public function testGetDiffSqlArray()
    {
        $result = $this->migrationsService->getDiffSqlArray();
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertStringStartsWith('INSERT INTO "kuma_translation"', $result[0]);
    }
}
