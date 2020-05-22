<?php

namespace Kunstmaan\MediaBundle\Tests\DependencyInjection;

use Kunstmaan\MediaBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public const DEFAULT_ALLOWED_EXTENSIONS = [
        'bmp',
        'csv',
        'doc',
        'docx',
        'gif',
        'ico',
        'jpeg',
        'jpg',
        'mkv',
        'mp3',
        'mp4',
        'mpeg',
        'ogg',
        'pdf',
        'png',
        'pps',
        'ppsx',
        'ppt',
        'pptx',
        'tif',
        'tiff',
        'txt',
        'wav',
        'webm',
        'webp',
        'xlsx',
    ];

    public const DEFAULT_IMAGE_EXTENSIONS = [
        'bmp',
        'ico',
        'jpeg',
        'jpg',
        'png',
        'tif',
        'tiff',
    ];

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'soundcloud_api_key' => 'thisismykey',
            'aviary_api_key' => 'apikey',
            'remote_video' => [
                'vimeo' => false,
                'youtube' => true,
                'dailymotion' => false,
            ],
            'enable_pdf_preview' => true,
            'blacklisted_extensions' => [],
            'allowed_extensions' => self::DEFAULT_ALLOWED_EXTENSIONS,
            'image_extensions' => self::DEFAULT_IMAGE_EXTENSIONS,
            'web_root' => '%kernel.project_dir%/web',
        ];

        if (Kernel::VERSION_ID >= 40000) {
            $array['web_root'] = '%kernel.project_dir%/public';
        }

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
