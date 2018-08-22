<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;

class LoaderTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loader = $this->getContainer()->get('kunstmaan_translator.service.translator.loader');
    }

    public function testLoad()
    {
        $catalogue = $this->loader->load('', 'en', 'validation');
        $messages = $catalogue->all('validation');
        $this->assertEquals($messages['validation.ok'], 'Everything ok');
        $this->assertEquals($catalogue->getLocale(), 'en');
    }
}
