<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\Transformer;

use Kunstmaan\MediaBundle\Helper\Transformer\PdfTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class PdfTransformerTest extends TestCase
{
    /** @var PdfTransformer */
    protected $object;

    /** @var Filesystem */
    protected $filesystem;

    /** @var string */
    protected $filesDir;

    /** @var string */
    protected $tempDir;

    protected function setUp()
    {
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not available.');
        }
        $this->filesDir = realpath(__DIR__ . '/../../Files');
        $this->tempDir = str_replace('/', DIRECTORY_SEPARATOR, sys_get_temp_dir().'/kunstmaan_media_test');

        $this->filesystem = new Filesystem();
        $this->removeTempDir();
        $this->filesystem->mkdir($this->tempDir);
        $this->object = new PdfTransformer(new \Imagick());
    }

    protected function tearDown()
    {
        if (!$this->filesystem) {
            return;
        }

        $this->removeTempDir();
    }

    public function testApplyWritesJpg()
    {
        system('which gs > /dev/null', $returnCode);
        if ($returnCode !== 0) {
            $this->markTestSkipped('Ghostscript is not installed.');
        }

        $pdfFilename = $this->tempDir . '/sample.pdf';
        $jpgFilename = $pdfFilename.'.jpg';

        $pdf = $this->filesDir . '/sample.pdf';
        $this->filesystem->copy($pdf, $pdfFilename);
        $this->assertFileExists($pdfFilename);

        $transformer = new PdfTransformer(new \Imagick());
        $absolutePath = $transformer->apply($pdfFilename);

        $this->assertEquals($jpgFilename, $absolutePath);
        $this->assertFileExists($jpgFilename);
        $this->assertNotEmpty(file_get_contents($jpgFilename));
    }

    public function testApplyDoesNotOverwriteExisting()
    {
        $pdfFilename = $this->tempDir . '/sample.pdf';
        $jpgFilename = $pdfFilename . '.jpg';

        $pdf = $this->filesDir . '/sample.pdf';
        $this->filesystem->copy($pdf, $pdfFilename);
        $this->assertFileExists($pdfFilename);

        $this->filesystem->touch($jpgFilename);

        $transformer = new PdfTransformer(new \Imagick());
        $absolutePath = $transformer->apply($pdfFilename);

        $this->assertEquals($jpgFilename, $absolutePath);
        $this->assertEmpty(file_get_contents($jpgFilename));
    }

    public function testGetPreviewFilename()
    {
        $pdfFilename = $this->tempDir . '/sample.pdf';
        $jpgFilename = $pdfFilename . '.jpg';

        $this->assertEquals($jpgFilename, $this->object->getPreviewFilename($pdfFilename));
    }

    private function removeTempDir()
    {
        if ($this->filesystem->exists($this->tempDir)) {
            $this->filesystem->remove($this->tempDir);
        }
    }
}
