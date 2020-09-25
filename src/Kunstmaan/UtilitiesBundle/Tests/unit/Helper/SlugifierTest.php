<?php

namespace Tests\Kunstmaan\NodeBundle\Helper;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use PHPUnit\Framework\TestCase;

/**
 * SlugifierTest
 */
class SlugifierTest extends TestCase
{
    /**
     * @var Slugifier
     */
    private $slugifier;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->slugifier = new Slugifier();
    }

    /**
     * @param string $text   The text to slugify
     * @param string $result The slug it should generate
     *
     * @dataProvider getSlugifyData
     */
    public function testSlugify($text, $result)
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
            ['polish-ążśźęćńół', 'polish-azszecnol'],
        ];
    }
}
