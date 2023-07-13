<?php

namespace Kunstmaan\SeoBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\SeoBundle\Form\SeoType;
use Kunstmaan\SeoBundle\Entity\Seo;
use PHPUnit\Framework\TestCase;

class SeoTest extends TestCase
{
    /**
     * @var Seo
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Seo();
    }

    public function testGetSetMetaAuthor()
    {
        $this->object->setMetaAuthor('Author Name');
        $this->assertSame('Author Name', $this->object->getMetaAuthor());
    }

    public function testGetSetMetaDescription()
    {
        $this->object->setMetaDescription('Meta Description');
        $this->assertSame('Meta Description', $this->object->getMetaDescription());
    }

    public function testGetSetMetaRobots()
    {
        $this->object->setMetaRobots('noindex, nofollow');
        $this->assertSame('noindex, nofollow', $this->object->getMetaRobots());
    }

    public function testGetSetExtraMetadata()
    {
        $this->object->setExtraMetadata('Extra Metadata');
        $this->assertSame('Extra Metadata', $this->object->getExtraMetadata());
    }

    public function testGetSetOgDescription()
    {
        $this->object->setOgDescription('OpenGraph description');
        $this->assertSame('OpenGraph description', $this->object->getOgDescription());
    }

    public function testGetSetOgImageWithImage()
    {
        $media = $this->createMock(Media::class);
        $this->object->setOgImage($media);
        $this->assertEquals($media, $this->object->getOgImage());
    }

    public function testGetSetOgImageWithNullValue()
    {
        $this->object->setOgImage(null);
        $this->assertEquals(null, $this->object->getOgImage());
    }

    public function testGetSetOgTitle()
    {
        $this->object->setOgTitle('OpenGraph title');
        $this->assertSame('OpenGraph title', $this->object->getOgTitle());
    }

    public function testGetSetOgType()
    {
        $this->object->setOgType('website');
        $this->assertSame('website', $this->object->getOgType());
    }

    public function testGetSetTwitterTitle()
    {
        $this->object->setTwitterTitle('twitter title');
        $this->assertSame('twitter title', $this->object->getTwitterTitle());
    }

    public function testGetSetTwitterDescription()
    {
        $this->object->setTwitterDescription('twitter description');
        $this->assertSame('twitter description', $this->object->getTwitterDescription());
    }

    public function testGetSetTwitterSite()
    {
        $this->object->setTwitterSite('@kunstmaan');
        $this->assertSame('@kunstmaan', $this->object->getTwitterSite());
    }

    public function testGetSetTwitterCreator()
    {
        $this->object->setTwitterCreator('@denbatte');
        $this->assertSame('@denbatte', $this->object->getTwitterCreator());
    }

    public function testGetSetTwitterImageWithImage()
    {
        $media = $this->createMock(Media::class);
        $this->object->setTwitterImage($media);
        $this->assertEquals($media, $this->object->getTwitterImage());
    }

    public function testGetSetTwitterImageWithNullValue()
    {
        $this->object->setTwitterImage(null);
        $this->assertEquals(null, $this->object->getTwitterImage());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(SeoType::class, $this->object->getDefaultAdminType());
    }

    public function testGettersSetters()
    {
        $this->object->setOgUrl('https://nasa.gov');
        $this->object->setMetaTitle('NASA');
        $this->object->setOgArticlePublisher('NASA PR dept');
        $this->object->setOgArticleSection('Mars');
        $this->object->setOgArticleAuthor('delboy1978uk');

        $this->assertSame('https://nasa.gov', $this->object->getOgUrl());
        $this->assertSame('NASA', $this->object->getMetaTitle());
        $this->assertSame('NASA PR dept', $this->object->getOgArticlePublisher());
        $this->assertSame('Mars', $this->object->getOgArticleSection());
        $this->assertSame('delboy1978uk', $this->object->getOgArticleAuthor());
    }
}
