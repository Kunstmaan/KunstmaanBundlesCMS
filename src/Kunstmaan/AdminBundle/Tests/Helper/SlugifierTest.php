<?php

namespace Kunstmaan\AdminBundle\Tests;

use Kunstmaan\AdminBundle\Helper\Slugifier;

class SlugifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSlugifyData
     */
    public function testSlugify($text, $default, $result)
    {
        if (!is_null($default)) {
            $this->assertEquals($result, Slugifier::slugify($text, $default));
        } else {
            $this->assertEquals($result, Slugifier::slugify($text));
        }
    }

    public function getSlugifyData()
    {
        return array(
            array('', '', ''),
            array('', null, 'n-a'),
            array('test', '', 'test'),
            array('een titel met spaties', '', 'een-titel-met-spaties'),
            array('à partir d\'aujourd\'hui', null, 'a-partir-d-aujourd-hui'),
            array('CaPs ShOulD be LoweRCasEd', null, 'caps-should-be-lowercased'),
        );
    }
}
