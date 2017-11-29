<?php

namespace Kunstmaan\NodeBundle\Tests;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;


/**
 * SlugifierTest
 */
class SlugifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
       $this->slugifier = new Slugifier();
    }
    /**
     * @param string $text    The text to slugify
     * @param string $result  The slug it should generate
     *
     * @dataProvider getSlugifyData
     */
    public function testSlugify($text,  $result)
    {
        $this->assertEquals($result, $this->slugifier->slugify($text));
    }

    /**
     * Provides data to the {@link testSlugify} function
     *
     * @return array
     */
    public function getSlugifyData()
    {
        return [
            ['', ''],
            ['test', 'test'],
            ['een titel met spaties', 'een-titel-met-spaties'],
            ['à partir d\'aujourd\'hui', 'a-partir-daujourdhui'],
            ['CaPs ShOulD be LoweRCasEd', 'caps-should-be-lowercased'],
            ['áàäåéèëíìïóòöúùüñßæ', 'aaaaeeeiiiooouuunssae'],
            ['polish-ążśźęćńół', 'polish-azszecnol']
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
}
