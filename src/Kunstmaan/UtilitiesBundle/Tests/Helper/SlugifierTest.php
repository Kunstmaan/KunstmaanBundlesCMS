<?php

namespace Kunstmaan\NodeBundle\Tests;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;

/**
 * SlugifierTest
 */
class SlugifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $text    The text to slugify
     * @param string $default The default alternative
     * @param string $result  The slug it should generate
     *
     * @dataProvider getSlugifyData
     * @covers Kunstmaan\UtilitiesBundle\Helper\Slugifier::slugify
     */
    public function testSlugify($text, $default, $result)
    {
        if (!is_null($default)) {
            $this->assertEquals($result, Slugifier::slugify($text, $default));
        } else {
            $this->assertEquals($result, Slugifier::slugify($text));
        }
    }

    /**
     * Provides data to the {@link testSlugify} function
     *
     * @return array
     */
    public function getSlugifyData()
    {
        return array(
            array('', '', ''),
            array('', null, 'n-a'),
            array('test', '', 'test'),
            array('een titel met spaties', '', 'een-titel-met-spaties'),
            array('à partir d\'aujourd\'hui', null, 'a-partir-d-aujourd-hui'),
            array('CaPs ShOulD be LoweRCasEd', null, 'caps-should-be-lowercased'),
            array('áàäåéèëíìïóòöúùüñßæ', null, 'aaaaeeeiiiooouuunssae'),,
            array('polish-ążśźęćńół', null, 'polish-azszecnol'),
        );
    }
}
