<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Migrations;

use Kunstmaan\TranslatorBundle\Tests\Unit\WebTestCase;

class MigrationsServiceTest extends WebTestCase
{
    private $migrationsService;

    public function setUp()
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

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
