<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use PHPUnit\Framework\TestCase;

class SlugifierTest extends TestCase
{
    private Slugifier $slugifier;

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
        $this->assertSame($result, $this->slugifier->slugify($text));
    }

    /**
     * Provides data to the {@link testSlugify} function
     */
    public function getSlugifyData(): \Iterator
    {
        yield ['', ''];
        yield ['test', 'test'];
        yield ['een titel met spaties', 'een-titel-met-spaties'];
        yield ['à partir d\'aujourd\'hui', 'a-partir-daujourdhui'];
        yield ['CaPs ShOulD be LoweRCasEd', 'caps-should-be-lowercased'];
        yield ['áàäåéèëíìïóòöúùüñßæ', 'aaaaeeeiiiooouuunssae'];
        yield ['polish-ążśźęćńół', 'polish-azszecnol'];
    }
}
