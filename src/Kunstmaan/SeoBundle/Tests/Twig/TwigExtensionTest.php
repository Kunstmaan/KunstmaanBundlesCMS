<?php

namespace Kunstmaan\SeoBundle\Tests\Entity;

use Kunstmaan\SeoBundle\Entity\Seo;
use Kunstmaan\SeoBundle\Twig\SeoTwigExtension;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class TwigExtensionTest extends TestCase
{
    protected $emMock;

    protected $entityMock;

    protected $seoRepoMock;

    protected function setUp(): void
    {
        $this->emMock = $this->createMock('\Doctrine\ORM\EntityManager');
    }

    public function testShouldReturnNameForEntityWhenNoSEO()
    {
        $name = 'OK';

        $this->entityWithName($name);
        $this->noSeoFound();

        $object = new SeoTwigExtension($this->emMock);

        $result = $object->getTitleFor($this->entityMock);

        $this->assertEquals($name, $result);
    }

    public function testShouldReturnNameForEntityWhenSEOWithTitleFound()
    {
        $nokName = 'NOK';
        $name = 'OK';

        $this->entityWithName($nokName);
        $this->seoFoundWithTitle($name);

        $object = new SeoTwigExtension($this->emMock);

        $result = $object->getTitleFor($this->entityMock);

        $this->assertEquals($name, $result);
    }

    public function testGetImageDimensionsWithValidFile()
    {
        $extension = new SeoTwigExtension($this->emMock);

        $dimensions = $extension->getImageDimensions(__DIR__ . '/../files/150.png');

        $this->assertSame(['width' => 150, 'height' => 150], $dimensions);
    }

    public function testGetImageDimensionsWithInvalidFile()
    {
        $extension = new SeoTwigExtension($this->emMock);

        $dimensions = $extension->getImageDimensions(__DIR__ . '/../files/unkown.png');

        $this->assertSame(['width' => null, 'height' => null], $dimensions);
    }

    public function testGetImageDimensionsWithCacheServiceAndCachedCall()
    {
        $cacheMock = $this->createMock(CacheItemPoolInterface::class);
        $cacheItemMock = $this->createMock(CacheItemInterface::class);
        $cacheItemMock->expects($this->once())->method('isHit')->willReturn(true);
        $cacheItemMock->expects($this->once())->method('get')->willReturn([151, 151]);

        $cacheMock->expects($this->once())->method('getItem')->withAnyParameters()->willReturn($cacheItemMock);

        $extension = new SeoTwigExtension($this->emMock);
        $extension->setRequestCache($cacheMock);

        $dimensions = $extension->getImageDimensions(__DIR__ . '/../files/150.png');

        $this->assertSame(['width' => 151, 'height' => 151], $dimensions);
    }

    public function testGetImageDimensionsWithCacheServiceAndNonCachedCall()
    {
        $cacheMock = $this->createMock(CacheItemPoolInterface::class);
        $cacheItemMock = $this->createMock(CacheItemInterface::class);
        $cacheItemMock->expects($this->once())->method('isHit')->willReturn(false);
        $cacheItemMock->expects($this->once())->method('set')->withAnyParameters();
        $cacheItemMock->expects($this->once())->method('get')->willReturn([150, 150]);

        $cacheMock->expects($this->once())->method('getItem')->withAnyParameters()->willReturn($cacheItemMock);
        $cacheMock->expects($this->once())->method('save')->with($cacheItemMock);

        $extension = new SeoTwigExtension($this->emMock);
        $extension->setRequestCache($cacheMock);

        $dimensions = $extension->getImageDimensions(__DIR__ . '/../files/150.png');

        $this->assertSame(['width' => 150, 'height' => 150], $dimensions);
    }

    /**
     * @param string $name
     */
    protected function entityWithName($name)
    {
        $this->entityMock = $this->createMock('Kunstmaan\NodeBundle\Entity\AbstractPage');
        $this->entityMock->expects($this->once())->method('getTitle')->willReturn($name);
    }

    protected function noSeoFound()
    {
        $this->ensureSeoRepoMock();
        $this->seoRepoMock->expects($this->once())
            ->method('findOrCreateFor')
            ->willReturn(null);

        $this->wireUpSeoRepo();
    }

    protected function ensureSeoRepoMock()
    {
        if (\is_null($this->seoRepoMock)) {
            $this->seoRepoMock = $this->createMock('Kunstmaan\SeoBundle\Repository\SeoRepository');
        }
    }

    protected function wireUpSeoRepo()
    {
        $this->emMock->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Seo::class))
            ->willReturn($this->seoRepoMock);
    }

    /**
     * @param string $title
     */
    protected function seoFoundWithTitle($title)
    {
        $this->ensureSeoRepoMock();

        $seoMock = new Seo();
        $seoMock->setRef($this->entityMock);
        $seoMock->setMetaTitle($title);

        $this->seoRepoMock->expects($this->once())
            ->method('findOrCreateFor')
            ->willReturn($seoMock);

        $this->wireUpSeoRepo();
    }
}
