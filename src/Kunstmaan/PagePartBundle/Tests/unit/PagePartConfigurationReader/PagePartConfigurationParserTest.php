<?php

namespace Kunstmaan\PagePartBundle\Tests\PagePartConfigurationReader;

use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationParser;
use Kunstmaan\PagePartBundle\Tests\unit\PagePartConfigurationReader\LocatingKernelStub;
use PHPUnit_Framework_TestCase;

class PagePartConfigurationParserTest extends PHPUnit_Framework_TestCase
{
    public function testParserKnowsAboutPresets()
    {
        $parser = new PagePartConfigurationParser(new LocatingKernelStub(), [
            'foo' => [
                'name' => 'Foo preset',
                'context' => 'main',
            ],
        ]);

        $value = $parser->parse('foo');

        $this->assertSame('Foo preset', $value->getName());
    }

    public function testExtendsWithinBundleWorks()
    {
        $parser = new PagePartConfigurationParser(new LocatingKernelStub());

        $value = $parser->parse('Bundle:main-extended');

        $this->assertCount(3, $value->getPossiblePagePartTypes());
    }

    public function testPresetExtendsBundle()
    {
        $parser = new PagePartConfigurationParser(new LocatingKernelStub(), [
            'foo' => [
                'name' => 'Foo preset',
                'context' => 'main',
                'extends' => 'Bundle:main',
            ],
        ]);

        $value = $parser->parse('foo');

        $this->assertCount(3, $value->getPossiblePagePartTypes());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCircularReferenceIsDetected()
    {
        $parser = new PagePartConfigurationParser(new LocatingKernelStub(), [
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
