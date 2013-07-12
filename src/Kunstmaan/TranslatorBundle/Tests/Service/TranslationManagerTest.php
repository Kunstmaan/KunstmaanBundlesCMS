<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;
use Kunstmaan\TranslatorBundle\Model\Translation\NewTranslation;

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

    public function testGetFirstDefaultDomainName()
    {
        $domainName = $this->translationManager->getFirstDefaultDomainName();
        $this->assertEquals($domainName, 'messages');
    }

    /**
     * @group manager
     */
    public function testNewTranslationsFromArray()
    {
        $post = array();
        $post[0]['keyword'] = 'article.new.keyword';
        $post[0]['domain'] = 'messages';
        $post[0]['locales']['nl']  = 'nieuw keyword';
        $post[0]['locales']['de']  = 'nueueueue keyword';
        $post[0]['locales']['eb']  = 'new keyword';

        $this->translationManager->newTranslationsFromArray($post);

    }

    /**
     * @group manager
     */
    public function testNewTranslation()
    {
        $post = array();
        $post['nl']  = 'nieuw keyword';
        $post['de']  = 'nueueueue keyword';
        $post['eb']  = 'new keyword';
        $newTranslation = new NewTranslation;
        $newTranslation->setLocales($post);
        $newTranslation->setDomain('messages');
        $newTranslation->setKeyword('keywords.new.keyword');

        $this->translationManager->newTranslation($newTranslation);

    }
}
