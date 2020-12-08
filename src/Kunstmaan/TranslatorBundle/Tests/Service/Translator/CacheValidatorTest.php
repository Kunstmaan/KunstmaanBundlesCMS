<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Translator;

use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use PHPUnit\Framework\TestCase;

class CacheValidatorTest extends TestCase
{
    private $cacheValidator;

    private $cacheDir = __DIR__ . '/../../app/cache';

    public function setUp(): void
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

    protected function tearDown(): void
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

    /**
     * @group time-sensitive
     */
    public function testOlderCacheFileMtDate()
    {
        $this->createDummyCachedFile();

        $expectedDate = (new \DateTime())->setTimestamp(time() - 3600);
        $actualDate = $this->cacheValidator->getOldestCachefileDate();

        $this->assertInstanceOf(\DateTime::class, $actualDate);
        $this->assertSame($expectedDate->getTimestamp(), $actualDate->getTimestamp());
    }

    public function testOlderCacheFileMtDateNoFiles()
    {
        $actualDate = $this->cacheValidator->getOldestCachefileDate();

        $this->assertNull($actualDate);
    }

    public function createDummyCachedFile()
    {
        touch(sprintf('%s/catalogue.test.php', $this->cacheDir));
        touch(sprintf('%s/catalogue2.test.php', $this->cacheDir), time() - 3600);
    }

    public function deleteDummyCachedFile()
    {
        $files = [
            sprintf('%s/catalogue.test.php', $this->cacheDir),
            sprintf('%s/catalogue2.test.php', $this->cacheDir),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
