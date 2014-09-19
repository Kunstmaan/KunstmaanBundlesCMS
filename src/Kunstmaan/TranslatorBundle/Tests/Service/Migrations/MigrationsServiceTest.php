<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service\Migrations;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;

class MigrationsServiceTest extends BaseTestCase
{

    private $translationRepository;
    private $migrationsService;

    public function setUp()
    {
        parent::setUp();
        $this->translationRepository = $this->getContainer()->get('kunstmaan_translator.repository.translation');
        $this->migrationsService = $this->getContainer()->get('kunstmaan_translator.service.migrations.migrations');
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
