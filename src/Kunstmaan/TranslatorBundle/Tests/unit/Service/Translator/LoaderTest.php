<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Translator;

use Kunstmaan\TranslatorBundle\Tests\unit\WebTestCase;

class LoaderTest extends WebTestCase
{
    public function setUp()
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        $this->loader = $container->get('kunstmaan_translator.service.translator.loader');
    }

    public function testLoad()
    {
        $catalogue = $this->loader->load('', 'en', 'validation');
        $messages = $catalogue->all('validation');
        $this->assertEquals($messages['validation.ok'], 'Everything ok');
        $this->assertEquals($catalogue->getLocale(), 'en');
    }
}
