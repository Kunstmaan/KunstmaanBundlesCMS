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
        $this->object->setBlacklistedExtensions([
            'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'php8',
            'phps', 'phpt', 'pht', 'phtm', 'phtml', 'phar', 'inc',
            'htaccess', 'htpasswd', 'htgroup',
            'shtml', 'shtm', 'cgi', 'pl', 'py', 'rb', 'sh',
            'asp', 'aspx', 'ashx', 'asmx', 'ascx', 'jsp', 'jspx', 'cfm', 'cfml',
        ]);
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

        $this->assertSame('abc123/', $method->invoke($this->object, $media));
    }

    public function testGetFileFolderPathIsEmptyWithoutUuid()
    {
        $media = new Media();
        $media->setOriginalFilename('orphan.jpg');

        $method = new \ReflectionMethod(FileHandler::class, 'getFileFolderPath');

        $this->assertSame('', $method->invoke($this->object, $media));
    }

    /**
     * @dataProvider blacklistedFileNameProvider
     */
    public function testBlacklistedExtensionsAreStoredAsTxt(string $originalFilename, string $expected)
    {
        $this->assertSame($expected, $this->getStoredFileName($originalFilename));
    }

    public function blacklistedFileNameProvider(): array
    {
        return [
            // The extension is lowercased before it is stored, so it must be checked
            // case-insensitively. See GHSA-p279-5wcv-45vq.
            'mixed case php' => ['webshell.pHp', 'webshell.txt'],
            'upper case php' => ['webshell.PHP', 'webshell.txt'],
            'lower case php' => ['webshell.php', 'webshell.txt'],
            'upper case htaccess' => ['file.HTACCESS', 'file.txt'],

            // Other extensions that are commonly executed as php.
            'phtml' => ['webshell.phtml', 'webshell.txt'],
            'php5' => ['webshell.php5', 'webshell.txt'],
            'phar' => ['webshell.phar', 'webshell.txt'],
            'phps' => ['webshell.phps', 'webshell.txt'],

            // The slugifier replaces inner dots, so a double extension can never keep an
            // executable segment.
            'double extension' => ['evil.php.jpg', 'evil-php.jpg'],
        ];
    }

    /**
     * @dataProvider allowedFileNameProvider
     */
    public function testAllowedExtensionsAreLeftAlone(string $originalFilename, string $expected)
    {
        $this->assertSame($expected, $this->getStoredFileName($originalFilename));
    }

    public function allowedFileNameProvider(): array
    {
        return [
            'lowercased extension' => ['holiday.JPG', 'holiday.jpg'],
            'regular image' => ['My Holiday Picture.jpeg', 'my-holiday-picture.jpeg'],
            'document' => ['report.pdf', 'report.pdf'],
            'no extension' => ['README', 'readme'],
        ];
    }

    public function testAllowListRejectsExtensionsOutsideTheList()
    {
        $this->object->setAllowedExtensions(['jpg', 'png']);

        $this->assertSame('doc.txt', $this->getStoredFileName('doc.pdf'));
    }

    public function testAllowListAcceptsExtensionsInTheList()
    {
        $this->object->setAllowedExtensions(['jpg', 'png']);

        $this->assertSame('photo.jpg', $this->getStoredFileName('photo.JPG'));
    }

    public function testBlacklistIsAppliedEvenWhenTheExtensionIsAllowListed()
    {
        $this->object->setAllowedExtensions(['jpg', 'php']);

        $this->assertSame('webshell.txt', $this->getStoredFileName('webshell.php'));
    }

    /**
     * The media is stored under the key of its original file, which is "<uuid>/<filename>". This
     * returns the file name part of that key.
     */
    private function getStoredFileName(string $originalFilename): string
    {
        $media = new Media();
        $media->setUuid('abc123');
        $media->setOriginalFilename($originalFilename);

        $key = $this->object->getOriginalFile($media)->getKey();

        $this->assertStringStartsWith('abc123/', $key);

        return substr($key, \strlen('abc123/'));
    }
}
