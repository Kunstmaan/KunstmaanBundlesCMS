<?php

namespace Kunstmaan\GeneratorBundle\Tests\Helper;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use PHPUnit\Framework\TestCase;

class GeneratorUtilsTest extends TestCase
{
    /**
     * @var GeneratorUtils
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new GeneratorUtils();
    }

    public function testCleanPrefixWhenPrefixEmpty()
    {
        $response = GeneratorUtils::cleanPrefix('');
        $this->assertEquals(null, $response);
    }

    public function testCleanPrefixShouldConvertToLowercase()
    {
        $response = GeneratorUtils::cleanPrefix('TEST');
        $this->assertEquals('test_', $response);
    }

    public function testCleanPrefixShouldAppendUnderscore()
    {
        $response = GeneratorUtils::cleanPrefix('test');
        $this->assertEquals('test_', $response);
    }

    public function testCleanPrefixShouldAppendUnderscoreOnlyWhenNeeded()
    {
        $response = GeneratorUtils::cleanPrefix('test_');
        $this->assertEquals('test_', $response);
    }

    public function testCleanPrefixShouldLeaveUnderscoresInPlace()
    {
        $response = GeneratorUtils::cleanPrefix('test_bundle');
        $this->assertEquals('test_bundle_', $response);
    }

    public function testCleanPrefixShouldLeaveSingleUnderscore()
    {
        $response = GeneratorUtils::cleanPrefix('test____');
        $this->assertEquals('test_', $response);
    }

    public function testShouldConvertOnlyUnderscoresToNull()
    {
        $response = GeneratorUtils::cleanPrefix('____');
        $this->assertEquals(null, $response);
    }

    public function testSpacesShouldCreateEmptyPrefix()
    {
        $response = GeneratorUtils::cleanPrefix('  ');
        $this->assertEquals(null, $response);
    }
}
