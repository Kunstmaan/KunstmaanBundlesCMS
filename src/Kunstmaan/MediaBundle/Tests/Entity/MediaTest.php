<?php

namespace Kunstmaan\MediaBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    /**
     * @var Media
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Media();
    }

    public function testSetFileSize()
    {
        $this->object->setFileSize(45);
        $this->assertSame('45b', $this->object->getFileSize());
        $this->object->setFileSize(64 * 1024);
        $this->assertSame('64kb', $this->object->getFileSize());
        $this->object->setFileSize(64 * 1024 * 1024);
        $this->assertSame('64mb', $this->object->getFileSize());
    }

    public function testGetSetUuid()
    {
        $this->object->setUuid('abc');
        $this->assertSame('abc', $this->object->getUuid());
    }

    public function testGetSetName()
    {
        $this->object->setName('name.jpg');
        $this->assertSame('name.jpg', $this->object->getName());
    }

    public function testGetSetLocation()
    {
        $this->object->setLocation('local');
        $this->assertSame('local', $this->object->getLocation());
    }

    public function testGetSetContentType()
    {
        $this->object->setContentType('image/jpeg');
        $this->assertSame('image/jpeg', $this->object->getContentType());
        $this->assertSame('jpeg', $this->object->getContentTypeShort());
    }

    public function testGetSetCreatedAt()
    {
        $date = new \DateTime();
        $this->object->setCreatedAt($date);
        $this->assertEquals($date, $this->object->getCreatedAt());
    }

    public function testGetSetUpdatedAt()
    {
        $date = new \DateTime();
        $this->object->setUpdatedAt($date);
        $this->assertEquals($date, $this->object->getUpdatedAt());
    }

    public function testGetContent()
    {
        $this->object->setContent('content');
        $this->assertSame('content', $this->object->getContent());
    }

    public function testGetSetFolder()
    {
        $folder = new Folder();
        $folder->setId(45);
        $this->object->setFolder($folder);
        $this->assertSame(45, $this->object->getFolder()->getId());
    }

    public function testGetSetDeleted()
    {
        $this->assertFalse($this->object->isDeleted());

        $this->object->setDeleted(true);
        $this->assertTrue($this->object->isDeleted());
    }

    public function testGetSetMetaDataAndValues()
    {
        $this->object->setTranslatableLocale('en');
        $meta = ['original_width' => 320, 'original_height' => 200];
        $this->object->setMetadata($meta);
        $this->assertSame($meta, $this->object->getMetadata());
        $this->assertSame(320, $this->object->getMetadataValue('original_width'));
        $this->assertSame(200, $this->object->getMetadataValue('original_height'));
        $this->object->setMetadataValue('original_width', 640);
        $this->assertSame(640, $this->object->getMetadataValue('original_width'));
    }

    public function testGetSetUrl()
    {
        $this->object->setUrl('http://domain.tld/path/name.ext');
        $this->assertSame('http://domain.tld/path/name.ext', $this->object->getUrl());
    }

    public function testGetSetOriginalFilename()
    {
        $this->object->setOriginalFilename('name.ext');
        $this->assertSame('name.ext', $this->object->getOriginalFilename());
    }

    public function testGetSetCopyright()
    {
        $this->object->setCopyright('(c) 2014 Kunstmaan All rights reserved');
        $this->assertSame('(c) 2014 Kunstmaan All rights reserved', $this->object->getCopyright());
    }

    public function testGetSetDescription()
    {
        $this->object->setDescription('Description of this picture');
        $this->assertSame('Description of this picture', $this->object->getDescription());
    }

    public function testGetSetRemoved()
    {
        $this->object->setRemovedFromFileSystem(true);
        $this->assertTrue($this->object->isRemovedFromFileSystem());
    }

    public function testGetSetFilesize()
    {
        $this->object->setFileSize(null);
        $this->assertEmpty($this->object->getFileSize());
        $this->object->setFileSize(123);
        $this->assertSame(123, $this->object->getFileSizeBytes());
    }

    public function testPreUpdate()
    {
        $this->object->preUpdate();
        $this->assertInstanceOf(\DateTime::class, $this->object->getUpdatedAt());
    }

    public function testPrePersist()
    {
        $this->object->setOriginalFilename('spongebob.jpg');
        $this->object->prePersist();
        $this->assertSame('spongebob.jpg', $this->object->getName());
    }
}
