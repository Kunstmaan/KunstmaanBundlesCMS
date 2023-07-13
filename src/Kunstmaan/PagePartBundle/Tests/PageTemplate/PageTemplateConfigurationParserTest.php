<?php

namespace Kunstmaan\PagePartBundle\Tests\PageTemplate;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class PageTemplateConfigurationParserTest extends TestCase
{
    public function testParseSymfony4Flow()
    {
        $kernel = $this->createMock(KernelInterface::class);
        $pageTemplateConfigurationParser = new PageTemplateConfigurationParser($kernel, [
            'contentpage' => [
                'name' => 'Content page',
                'rows' => [
                    ['regions' => [['name' => 'main', 'span' => 12]]],
                ],
                'template' => 'Pages\\ContentPage\\pagetemplate.html.twig',
            ],
        ]);

        $result = $pageTemplateConfigurationParser->parse('contentpage');
        $this->assertInstanceOf(PageTemplate::class, $result);
        $this->assertSame('Content page', $result->getName());
    }

    public function testParseSymfony3Flow()
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('locateResource')->willReturn(__DIR__ . '/Resources/config/pagetemplates/test.yml');

        $pageTemplateConfigurationParser = new PageTemplateConfigurationParser($kernel, []);

        $result = $pageTemplateConfigurationParser->parse('MyWebsiteBundle:test');
        $this->assertInstanceOf(PageTemplate::class, $result);
        $this->assertSame('Test page', $result->getName());
    }
}
