<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\MediaTokenTransformer;
use PHPUnit_Framework_TestCase;

/**
 * Class MediaTokenTransformerTest
 */
class MediaTokenTransformerTest extends PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $type = new MediaTokenTransformer();

        $this->assertEmpty($type->reverseTransform(''));

        $content = '<body><div>
<img data-src="https://static1.squarespace.com/static/54ed646fe4b0d65dc0187c06/555e813be4b0e50936283863/5879286e1e5b6c9b64f8357b/1484335217742/rick_morty_1200.jpg?format=500w"><img data-src="https://cdn.vox-cdn.com/thumbor/1kKyzwmocR6pu9ijSIl_l1XP0PY=/0x0:1280x720/1200x675/filters:focal(470x259:674x463)/cdn.vox-cdn.com/uploads/chorus_image/image/58089103/r_m_sauce.0.jpg"><img data-src="https://images.complex.com/complex/images/c_limit,w_680/fl_lossy,pg_1,q_auto/jcazxrnckmlyze5sr1ws/rick-morty">
</div></body>';

        $expected = str_replace('data-src', 'src', $content);
        $expected = str_replace("\n", '', $expected);

        $transformed = trim($type->transform($content));
        $transformed = str_replace("\n", '', $transformed);
        $reversed = trim($type->reverseTransform($transformed));
        $reversed = str_replace("\n", '', $reversed);

        $expectReversed = '<div>
<img src="https://static1.squarespace.com/static/54ed646fe4b0d65dc0187c06/555e813be4b0e50936283863/5879286e1e5b6c9b64f8357b/1484335217742/rick_morty_1200.jpg?format=500w" data-src="https://static1.squarespace.com/static/54ed646fe4b0d65dc0187c06/555e813be4b0e50936283863/5879286e1e5b6c9b64f8357b/1484335217742/rick_morty_1200.jpg?format=500w"><img src="https://cdn.vox-cdn.com/thumbor/1kKyzwmocR6pu9ijSIl_l1XP0PY=/0x0:1280x720/1200x675/filters:focal(470x259:674x463)/cdn.vox-cdn.com/uploads/chorus_image/image/58089103/r_m_sauce.0.jpg" data-src="https://cdn.vox-cdn.com/thumbor/1kKyzwmocR6pu9ijSIl_l1XP0PY=/0x0:1280x720/1200x675/filters:focal(470x259:674x463)/cdn.vox-cdn.com/uploads/chorus_image/image/58089103/r_m_sauce.0.jpg"><img src="https://images.complex.com/complex/images/c_limit,w_680/fl_lossy,pg_1,q_auto/jcazxrnckmlyze5sr1ws/rick-morty" data-src="https://images.complex.com/complex/images/c_limit,w_680/fl_lossy,pg_1,q_auto/jcazxrnckmlyze5sr1ws/rick-morty">
</div>';

        $expectReversed = str_replace("\n", '', $expectReversed);

        $this->assertEquals($expected, $transformed);
        $this->assertEquals($expectReversed, $reversed);
    }
}
