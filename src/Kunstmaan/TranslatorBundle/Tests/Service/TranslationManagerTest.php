<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;

class TranslationManagerTest extends BaseTestCase
{

    private $translationManager;

    public function setUp()
    {
        parent::setUp();
        $this->translationManager = $this->getContainer()->get('kunstmaan_translator.service.manager');
    }

    /**
     * @group manager
     */
    public function testGetAllDomains()
    {
        $domains = $this->translationManager->getAllDomains();
        $this->assertTrue(is_array($domains));
        $this->assertGreaterThan(0, count($domains));
    }

    /**
     * @group manager
     */
    public function testGetTranslationGroupsByDomain()
    {
        $groups = $this->translationManager->getTranslationGroupsByDomain('messages');
        $this->assertInstanceOf('Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup', $groups->first());
    }
}