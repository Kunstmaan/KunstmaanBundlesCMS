<?php

namespace Kunstmaan\TaggingBundle\Tests\DependencyInjection;

use Kunstmaan\TranslatorBundle\DependencyInjection\KunstmaanTranslatorExtension;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanTranslatorExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var KunstmaanTranslatorExtension
     */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
    }

    public function testEnabledByDefault()
    {
        $container = $this->getContainer();
        $this->extension->load(array('kuma_translator' => array('managed_locales' => array('nl'))), $container);
        $this->assertTrue($container->getParameter('kuma_translator.enabled'));
    }

    public function testDisabled()
    {
        $container = $this->getContainer();
        $this->extension->load(array('kuma_translator' => array('enabled' => false)), $container);
        $this->assertFalse($container->hasParameter('kuma_translator.enabled'));
    }

    /**
     * @return KunstmaanTranslatorExtension
     */
    protected function getExtension()
    {
        return new KunstmaanTranslatorExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.root_dir', 'src/Kunstmaan/ArticleBundle');
        $container->setParameter('kernel.bundles', array(new \Kunstmaan\ArticleBundle\KunstmaanArticleBundle()));
        $container->setParameter('kernel.debug', true);
        $container->setParameter('defaultlocale', 'en');

        return $container;
    }
}
