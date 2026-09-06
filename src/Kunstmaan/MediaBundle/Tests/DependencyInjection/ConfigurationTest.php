<?php

namespace Kunstmaan\MediaBundle\Tests\DependencyInjection;

use Kunstmaan\MediaBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): \Symfony\Component\Config\Definition\ConfigurationInterface
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
            'allowed_extensions' => [],
            'web_root' => '%kernel.project_dir%/public',
            'cropping_views' => [
                'default' => [
                    ['name' => 'desktop', 'width' => 1, 'height' => 1, 'lock_ratio' => true],
                ],
                'custom_views' => [],
                'focus_point_classes' => [],
            ],
        ];

        $expectedConfig = $array;

        $this->assertProcessedConfigurationEquals([$array], $expectedConfig);
    }

    public function testExecutableExtensionsAreBlacklistedByDefault()
    {
        $processed = (new Processor())->processConfiguration($this->getConfiguration(), [[]]);

        foreach (['php', 'phtml', 'php5', 'phar', 'phps', 'htaccess'] as $extension) {
            $this->assertContains($extension, $processed['blacklisted_extensions']);
        }

        // The allow list is opt-in, by default only the blacklist is applied.
        $this->assertSame([], $processed['allowed_extensions']);
    }
}
