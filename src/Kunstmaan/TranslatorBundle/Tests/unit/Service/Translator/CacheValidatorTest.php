<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use PHPUnit\Framework\TestCase;

class CacheValidatorTest extends TestCase
{
    private $cacheValidator;

    private $cacheDir = __DIR__.'/../../app/cache';

    public function setUp()
    {
        $date = new \DateTimeImmutable();
        $yesterday = $date->modify('-1 day');

        $translationRepository = $this->createMock(TranslationRepository::class);
        $translationRepository->method('getLastChangedTranslationDate')->willReturn($yesterday);

        $this->cacheValidator = new CacheValidator();
        $this->cacheValidator->setTranslationRepository($translationRepository);

        $this->cacheDir = sprintf('%s/translations/', $this->cacheDir);
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

        $this->cacheValidator->setCacheDir($this->cacheDir);
    }

    public function tearDown()
    {
        $this->deleteDummyCachedFile();
    }

    /**
     * @group cacher
     */
    public function testIsCacheFreshWithNoCache()
    {
        // cache is always fresh, because there is none
        $fresh = $this->cacheValidator->isCacheFresh();
        $this->assertTrue($fresh);
    }

    /**
     * @group cacher
     */
    public function testIsCacheFreshWithExistingCache()
    {
        $this->createDummyCachedFile();
        $fresh = $this->cacheValidator->isCacheFresh();
        $this->assertTrue($fresh);
    }

    public function createDummyCachedFile()
    {
        touch(sprintf('%s/catalogue.test.php', $this->cacheDir));
    }

    public function deleteDummyCachedFile()
    {
        $file = sprintf('%s/catalogue.test.php', $this->cacheDir);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
