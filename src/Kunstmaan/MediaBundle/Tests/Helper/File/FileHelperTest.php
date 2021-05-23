<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\File;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelperTest extends TestCase
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var FileHelper
     */
    protected $object;

    protected function setUp(): void
    {
        $this->media = new Media();
        $this->object = new FileHelper($this->media, '/uploads/media/');
    }

    public function testGetSetName()
    {
        $this->object->setName('name');
        $this->assertEquals('name', $this->object->getName());
    }

    public function testGetSetFile()
    {
        $path = tempnam('/tmp', 'kunstmaan-media');
        $file = new UploadedFile($path, 'test');
        $this->object->setFile($file);
        $this->assertEquals($file, $this->object->getFile());
        unlink($path);
    }

    public function testGetSetFolder()
    {
        $folder = new Folder();
        $this->object->setFolder($folder);
        $this->assertEquals($folder, $this->object->getFolder());
    }

    public function testGetSetCopyright()
    {
        $this->object->setCopyright('copyright');
        $this->assertEquals('copyright', $this->object->getCopyright());
    }

    public function testGetSetDescription()
    {
        $this->object->setDescription('description');
        $this->assertEquals('description', $this->object->getDescription());
    }

    public function testGetSetOriginalFilename()
    {
        $this->object->setOriginalFilename('image.jpg');
        $this->assertEquals('image.jpg', $this->object->getOriginalFilename());
    }

    public function testGetMedia()
    {
        $this->media->setId(1);
        $media = $this->object->getMedia();
        $this->assertEquals($this->media, $media);
    }
}
