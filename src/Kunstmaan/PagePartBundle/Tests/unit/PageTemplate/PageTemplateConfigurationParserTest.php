<?php

namespace Kunstmaan\PagePartBundle\Tests\PagePartConfigurationReader;

use Codeception\Test\Unit;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationParser;
use Symfony\Component\HttpKernel\KernelInterface;

class PageTemplateConfigurationParserTest extends Unit
{
    public function testParseSf4Flow()
    {
        $kernel = $this->makeEmpty(KernelInterface::class);
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
        $this->assertEquals('Content page', $result->getName());
    }

    public function testParseSf3Flow()
    {
        $kernel = $this->makeEmpty(KernelInterface::class, [
            'locateResource' => __DIR__ . '/Resources/config/pagetemplates/test.yml',
        ]);

        $pageTemplateConfigurationParser = new PageTemplateConfigurationParser($kernel, []);

        $result = $pageTemplateConfigurationParser->parse('MyWebsiteBundle:test');
        $this->assertInstanceOf(PageTemplate::class, $result);
        $this->assertEquals('Test page', $result->getName());
    }
}
