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
            'web_root' => '%kernel.project_dir%/web',
            'cropping_views' => [
                'default' => [
                    ['name' => 'desktop', 'width' => 1, 'height' => 1, 'lockRatio' => true, 'byFocusPoint' => false],
                ],
                'custom_views' => [],
            ],
        ];

        if (Kernel::VERSION_ID >= 40000) {
            $array['web_root'] = '%kernel.project_dir%/public';
        }

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
