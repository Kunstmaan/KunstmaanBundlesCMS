<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\File;

use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Mime\MimeTypes;

class FileHandlerTest extends TestCase
{
    /** @var FileHandler */
    private $object;

    /** @var string */
    private $mediaRoot;

    protected function setUp(): void
    {
        $this->mediaRoot = sys_get_temp_dir() . '/kunstmaan-media-' . uniqid();
        mkdir($this->mediaRoot, 0777, true);

        $this->object = new FileHandler(0, new MimeTypes());
        $this->object->setSlugifier(new Slugifier());
        $this->object->setMediaPath('/uploads/media/');
        $this->object->setBlacklistedExtensions(['php', 'htaccess']);
        $this->object->setFileSystem(new Filesystem(new Local($this->mediaRoot, true)));
    }

    protected function tearDown(): void
    {
        (new SymfonyFilesystem())->remove($this->mediaRoot);
    }

    private function createMediaFile(string $uuid, string $fileName): Media
    {
        mkdir($this->mediaRoot . '/' . $uuid, 0777, true);
        file_put_contents($this->mediaRoot . '/' . $uuid . '/' . $fileName, 'dummy content');

        $media = new Media();
        $media->setUuid($uuid);
        $media->setOriginalFilename($fileName);

        return $media;
    }

    public function testRemoveMediaRemovesTheFile()
    {
        $media = $this->createMediaFile('abc123', 'photo.jpg');

        $this->object->removeMedia($media);

        $this->assertFileDoesNotExist($this->mediaRoot . '/abc123/photo.jpg');
        $this->assertTrue($media->isRemovedFromFileSystem());
    }

    public function testRemoveMediaRemovesTheContainingFolderWhenItIsEmpty()
    {
        $media = $this->createMediaFile('abc123', 'photo.jpg');

        $this->object->removeMedia($media);

        $this->assertDirectoryDoesNotExist($this->mediaRoot . '/abc123');
    }

    public function testRemoveMediaKeepsTheContainingFolderWhenOtherFilesRemain()
    {
        $media = $this->createMediaFile('abc123', 'photo.jpg');
        file_put_contents($this->mediaRoot . '/abc123/keep-me.jpg', 'dummy content');

        $this->object->removeMedia($media);

        $this->assertFileDoesNotExist($this->mediaRoot . '/abc123/photo.jpg');
        $this->assertDirectoryExists($this->mediaRoot . '/abc123');
        $this->assertFileExists($this->mediaRoot . '/abc123/keep-me.jpg');
    }

    public function testRemoveMediaLeavesUnrelatedFoldersAlone()
    {
        $media = $this->createMediaFile('abc123', 'photo.jpg');
        $this->createMediaFile('def456', 'other.jpg');

        $this->object->removeMedia($media);

        $this->assertDirectoryDoesNotExist($this->mediaRoot . '/abc123');
        $this->assertFileExists($this->mediaRoot . '/def456/other.jpg');
    }

    /**
     * Without a uuid the folder path resolves to the root of the media filesystem, removing it
     * would wipe every media folder.
     */
    public function testRemoveMediaWithoutUuidDoesNotTouchTheMediaRoot()
    {
        $this->createMediaFile('def456', 'other.jpg');

        $media = new Media();
        $media->setOriginalFilename('orphan.jpg');

        $this->object->removeMedia($media);

        $this->assertDirectoryExists($this->mediaRoot);
        $this->assertFileExists($this->mediaRoot . '/def456/other.jpg');
    }

    public function testGetFileFolderPathReturnsTheUuidFolder()
    {
        $media = new Media();
        $media->setUuid('abc123');
        $media->setOriginalFilename('My Photo.JPG');

        $method = new \ReflectionMethod(FileHandler::class, 'getFileFolderPath');
        $method->setAccessible(true);

        $this->assertSame('abc123/', $method->invoke($this->object, $media));
    }

    public function testGetFileFolderPathIsEmptyWithoutUuid()
    {
        $media = new Media();
        $media->setOriginalFilename('orphan.jpg');

        $method = new \ReflectionMethod(FileHandler::class, 'getFileFolderPath');
        $method->setAccessible(true);

        $this->assertSame('', $method->invoke($this->object, $media));
    }
}
