<?php

namespace Kunstmaan\MediaBundle\Tests\DependencyInjection;

use Kunstmaan\MediaBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

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
                    ['name' => 'desktop', 'width' => 1, 'height' => 1, 'lock_ratio' => true],
                ],
                'custom_views' => [],
                'focus_point_classes' => [],
            ],
        ];

        if (Kernel::VERSION_ID >= 40000) {
            $array['web_root'] = '%kernel.project_dir%/public';
        }

        $expectedConfig = $array;
        $expectedConfig['aviary_api_key'] = null;

        $this->assertProcessedConfigurationEquals([$array], $expectedConfig);
    }

    /**
     * @group legacy
     * @expectedDeprecation The child node "aviary_api_key" at path "kunstmaan_media" is deprecated. Because the aviary service is discontinued.
     */
    public function testDeprecatedAviaryConfig()
    {
        $this->assertConfigurationIsValid([['aviary_api_key' => 'deprecated_key']]);
    }
}
