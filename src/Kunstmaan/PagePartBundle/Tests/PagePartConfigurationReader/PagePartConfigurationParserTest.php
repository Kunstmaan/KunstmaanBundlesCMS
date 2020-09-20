<?php

namespace Kunstmaan\PagePartBundle\Tests\PagePartConfigurationReader;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class PagePartConfigurationParserTest extends TestCase
{
    public function testParseSymfony4Flow()
    {
        $kernel = $this->createMock(KernelInterface::class);
        $pagePartConfigurationParser = new PagePartConfigurationParser($kernel, [
            'main' => [
                'name' => 'Main content',
                'context' => 'main',
                'types' => [
                    ['name' => 'FooBar', 'class' => 'Foo\BarPagePart'],
                    ['name' => 'Foo', 'class' => 'FooPagePart'],
                    ['name' => 'Bar', 'class' => 'BarPagePart'],
                ],
            ],
        ]);

        $result = $pagePartConfigurationParser->parse('main');
        $this->assertInstanceOf(PagePartAdminConfiguratorInterface::class, $result);
        $this->assertEquals('Main content', $result->getName());
    }

    public function testParseSymfony3Flow()
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('locateResource')->willReturn(__DIR__ . '/Resources/config/pageparts/main.yml');

        $pagePartConfigurationParser = new PagePartConfigurationParser($kernel, []);

        $result = $pagePartConfigurationParser->parse('MyWebsiteBundle:main');
        $this->assertInstanceOf(PagePartAdminConfiguratorInterface::class, $result);
        $this->assertEquals('Main content', $result->getName());
    }

    public function testPresetExtendsBundle()
    {
        $kernel = $this->createMock(KernelInterface::class);
        $pagePartConfigurationParser = new PagePartConfigurationParser($kernel, [
            'foo' => [
                'name' => 'Foo content',
                'context' => 'foo',
                'extends' => 'main',
                'types' => [
                    ['name' => 'FooBar', 'class' => 'Foo\BarPagePart'],
                    ['name' => 'Foo', 'class' => 'FooPagePart'],
                    ['name' => 'Bar', 'class' => 'BarPagePart'],
                ],
            ],
            'main' => [
                'name' => 'Main content',
                'context' => 'main',
                'types' => [
                    ['name' => 'Header', 'class' => 'HeaderPagePart'],
                ],
            ],
        ]
        );

        $value = $pagePartConfigurationParser->parse('foo');

        $this->assertCount(4, $value->getPossiblePagePartTypes());
    }

    public function testCircularReferenceIsDetected()
    {
        $this->expectException(\RuntimeException::class);
        $kernel = $this->createMock(KernelInterface::class);

        $parser = new PagePartConfigurationParser($kernel, [
            'foo' => [
                'name' => 'Foo preset',
                'extends' => 'bar',
            ],
            'bar' => [
                'name' => 'Bar preset',
                'extends' => 'baz',
            ],
            'baz' => [
                'name' => 'Baz preset',
                'extends' => 'foo',
            ],
        ]);

        $parser->parse('foo');
    }
}
