<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;

class DoctrineORMStasherTest extends BaseTestCase
{

    private $doctrineOrmStasher;
    private $translationDomainRepository;

    public function setUp()
    {
        parent::setUp();
        $this->doctrineOrmStasher = $this->getContainer()->get('kunstmaan_translator.service.stasher.doctrine_orm');
        $this->translationDomainRepository = $this->getContainer()->get('kunstmaan_translator.repository.translation_domain');
    }

    public function testCreateTranslationDomain()
    {
        $this->doctrineOrmStasher->createTranslationDomain('zeer_belangerijk');
        $this->doctrineOrmStasher->flush();
        $domain = $this->translationDomainRepository->findOneByName('zeer_belangerijk');
        $this->assertInstanceOf('\Kunstmaan\TranslatorBundle\Entity\TranslationDomain', $domain);
    }

    public function testGetTranslationGroupsByDomain()
    {
        $groups = $this->doctrineOrmStasher->getTranslationGroupsByDomain('validation');
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $groups);
        $this->assertInstanceOf('\Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup', $groups->first());
        $this->assertInstanceOf('\Kunstmaan\TranslatorBundle\Entity\Translation', $groups->first()->getTranslations()->first());
    }

    public function testGetTranslationGroupsByDomainNonExisting()
    {
        $groups = $this->doctrineOrmStasher->getTranslationGroupsByDomain('nonexistingdomain');
        $this->assertTrue(empty($groups));
    }
}