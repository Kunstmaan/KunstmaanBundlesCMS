<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\MediaTokenTransformer;

/**
 * Class MediaTokenTransformerTest
 *
 * @package Kunstmaan\AdminBundle\Tests\Form
 */
class MediaTokenTransformerTest extends \PHPUnit_Framework_TestCase
{

    public function testTransformCopiesDataSrcToSrc()
    {
        $transformer = new MediaTokenTransformer();

        $content = '<img src="[M1]" data-src="image.jpg?token=[M1]">';

        $expected = '<img src="image.jpg?token=%5BM1%5D">';

        $this->assertContains($expected, $transformer->transform($content));
    }

    public function testReverseTransformSetsSrcAndDataSrc()
    {
        $transformer = new MediaTokenTransformer();

        $content = '<img src="image.jpg?token=%5BM1%5D">';

        $expected = '<img src="[M1]" data-src="image.jpg?token=[M1]">';

        $this->assertEquals($expected, $transformer->reverseTransform($content));
    }

    public function testReverseTransformPreservesAnchorHrefs()
    {
        $transformer = new MediaTokenTransformer();

        $content = '<a href="%5BNT1%5D"></a>';

        $expected = '<a href="[NT1]"></a>';

        $this->assertEquals($expected, $transformer->reverseTransform($content));
    }

}
