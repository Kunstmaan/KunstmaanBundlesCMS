<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\unit\BaseTestCase;

class CacheValidatorTest extends BaseTestCase
{
    private $cacheValidator;

    private $languages;

    private $cacheDir;

    public function setUp()
    {
        parent::setUp();
        $this->cacheValidator = $this->getContainer()->get('kunstmaan_translator.service.translator.cache_validator');

        $this->languages = array('nl', 'fr', 'de', 'es');
        $this->cacheDir = sprintf('%s/translations/', $this->getContainer()->getParameter('kernel.cache_dir'));
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * @group cacher
     */
    public function testNoGetOldestCachefileDate()
    {
        $data = $this->cacheValidator->getOldestCachefileDate();
        $this->assertNull($data);
    }

    /**
     * @group cacher
     */
    public function testGetOldestCachefileDate()
    {
        $this->createDummyTranslationFiles();
        $date = $this->cacheValidator->getOldestCachefileDate();
        $this->deleteDummyTranslationFiles();
        $this->assertInstanceOf('\DateTime', $date);
    }

    /**
     * @group cacher
     */
    public function testisCacheFresh()
    {
        // cache is always fresh, because there is none
        $fresh = $this->cacheValidator->isCacheFresh();
        $this->assertTrue($fresh);
    }

    public function createDummyTranslationFiles()
    {
        foreach ($this->languages as $language) {
            $command = sprintf('touch %s/catalogue.%s.php', $this->cacheDir, $language);
            exec($command);
        }
    }

    public function deleteDummyTranslationFiles()
    {
        foreach ($this->languages as $language) {
            $file = sprintf('%s/catalogue.%s.php', $this->cacheDir, $language);
            unlink($file);
        }
    }
}
