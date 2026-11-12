<?php

namespace Kunstmaan\MediaBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Controller\MediaController;
use Kunstmaan\MediaBundle\Helper\FolderManager;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaControllerTest extends TestCase
{
    /** @var MediaController */
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new MediaController(
            $this->createMock(MediaManager::class),
            $this->createMock(FolderManager::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EntityManagerInterface::class)
        );

        $this->controller->setContainer(new ServiceLocator([
            'kunstmaan_utilities.slugifier' => static function () {
                return new Slugifier();
            },
        ]));
    }

    private function getSafeUploadFileName(string $fileName): ?string
    {
        $method = new \ReflectionMethod(MediaController::class, 'getSafeUploadFileName');
        $method->setAccessible(true);

        return $method->invoke($this->controller, $fileName);
    }

    /**
     * The bulk upload file name comes straight from the request and was concatenated into a path
     * without any sanitisation, so it could point outside the temp upload directory.
     *
     * @dataProvider dataTraversalFileNames
     */
    public function testPathInformationIsStripped(string $fileName, string $expected)
    {
        $this->assertSame($expected, $this->getSafeUploadFileName($fileName));
    }

    public function dataTraversalFileNames(): array
    {
        return [
            'relative traversal' => ['../../foo.jpg', 'foo.jpg'],
            'absolute path' => ['/etc/passwd', 'passwd'],
            'nested path' => ['a/b/c.png', 'c.png'],
            'windows separators' => ['..\\..\\foo.jpg', 'foo.jpg'],
            'null byte' => ["foo\0.jpg", 'foo.jpg'],
            'traversal without extension' => ['../../foo', 'foo'],
        ];
    }

    /**
     * @dataProvider dataRejectedFileNames
     */
    public function testFileNamesWithoutAnythingUsableAreRejected(string $fileName)
    {
        $this->assertNull($this->getSafeUploadFileName($fileName));
    }

    public function dataRejectedFileNames(): array
    {
        return [
            'empty' => [''],
            'current dir' => ['.'],
            'parent dir' => ['..'],
            'only separators' => ['/'],
            'only whitespace' => ['   '],
        ];
    }

    /**
     * @dataProvider dataNormalFileNames
     */
    public function testRegularFileNamesAreNormalised(string $fileName, string $expected)
    {
        $this->assertSame($expected, $this->getSafeUploadFileName($fileName));
    }

    public function dataNormalFileNames(): array
    {
        return [
            'simple' => ['photo.jpg', 'photo.jpg'],
            'spaces' => ['My Holiday Photo.jpg', 'my-holiday-photo.jpg'],
            'uppercase extension' => ['photo.JPG', 'photo.jpg'],
            'accents' => ['évidence.png', 'evidence.png'],
            'no extension' => ['README', 'readme'],
        ];
    }
}
