<?php

namespace Kunstmaan\SeoBundle\Tests\Entity;

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
        $this->assertEquals('Author Name', $this->object->getMetaAuthor());
    }

    public function testGetSetMetaDescription()
    {
        $this->object->setMetaDescription('Meta Description');
        $this->assertEquals('Meta Description', $this->object->getMetaDescription());
    }

    public function testGetSetMetaRobots()
    {
        $this->object->setMetaRobots('noindex, nofollow');
        $this->assertEquals('noindex, nofollow', $this->object->getMetaRobots());
    }

    public function testGetSetExtraMetadata()
    {
        $this->object->setExtraMetadata('Extra Metadata');
        $this->assertEquals('Extra Metadata', $this->object->getExtraMetadata());
    }

    public function testGetSetOgDescription()
    {
        $this->object->setOgDescription('OpenGraph description');
        $this->assertEquals('OpenGraph description', $this->object->getOgDescription());
    }

    public function testGetSetOgImageWithImage()
    {
        $media = $this->createMock('Kunstmaan\MediaBundle\Entity\Media');
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
        $this->assertEquals('OpenGraph title', $this->object->getOgTitle());
    }

    public function testGetSetOgType()
    {
        $this->object->setOgType('website');
        $this->assertEquals('website', $this->object->getOgType());
    }

    public function testGetSetTwitterTitle()
    {
        $this->object->setTwitterTitle('twitter title');
        $this->assertEquals('twitter title', $this->object->getTwitterTitle());
    }

    public function testGetSetTwitterDescription()
    {
        $this->object->setTwitterDescription('twitter description');
        $this->assertEquals('twitter description', $this->object->getTwitterDescription());
    }

    public function testGetSetTwitterSite()
    {
        $this->object->setTwitterSite('@kunstmaan');
        $this->assertEquals('@kunstmaan', $this->object->getTwitterSite());
    }

    public function testGetSetTwitterCreator()
    {
        $this->object->setTwitterCreator('@denbatte');
        $this->assertEquals('@denbatte', $this->object->getTwitterCreator());
    }

    public function testGetSetTwitterImageWithImage()
    {
        $media = $this->createMock('Kunstmaan\MediaBundle\Entity\Media');
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
        $this->assertEquals('Kunstmaan\SeoBundle\Form\SeoType', $this->object->getDefaultAdminType());
    }

    public function testGettersSetters()
    {
        $this->object->setOgUrl('https://nasa.gov');
        $this->object->setMetaTitle('NASA');
        $this->object->setOgArticlePublisher('NASA PR dept');
        $this->object->setOgArticleSection('Mars');
        $this->object->setOgArticleAuthor('delboy1978uk');

        $this->assertEquals('https://nasa.gov', $this->object->getOgUrl());
        $this->assertEquals('NASA', $this->object->getMetaTitle());
        $this->assertEquals('NASA PR dept', $this->object->getOgArticlePublisher());
        $this->assertEquals('Mars', $this->object->getOgArticleSection());
        $this->assertEquals('delboy1978uk', $this->object->getOgArticleAuthor());
    }
}
