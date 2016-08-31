<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-26 at 13:21:32.
 */
class FilterBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FilterBuilder();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::add
     */
    public function testAdd()
    {
        $result = $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', array('option1' => 'value1'));

        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\FilterBuilder', $result);
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::get
     */
    public function testGet()
    {
        $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', array('option1' => 'value1'));
        $definition = $this->object->get('columnName');

        $this->assertArrayHasKey('type', $definition);
        $this->assertArrayHasKey('filtername', $definition);
        $this->assertArrayHasKey('options', $definition);
        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType', $definition['type']);
        $this->assertEquals('filterName', $definition['filtername']);
        $this->assertEquals(array('option1' => 'value1'), $definition['options']);
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::remove
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::get
     */
    public function testRemove()
    {
        $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', array('option1' => 'value1'));
        $definition = $this->object->get('columnName');
        $this->assertNotNull($definition);

        $this->object->remove('columnName');
        $definition = $this->object->get('columnName');
        $this->assertNull($definition);
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::has
     */
    public function testHas()
    {
        $this->assertFalse($this->object->has('columnName'));
        $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', array('option1' => 'value1'));
        $this->assertTrue($this->object->has('columnName'));
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::getFilterDefinitions
     */
    public function testGetFilterDefinitions()
    {
        $filter = new StringFilterType('string', 'e');
        $filterDef = array('columnName' => array('type' => $filter, 'options' => array('option1' => 'value1'), 'filtername' => 'filterName'));
        $this->object->add('columnName', $filter, 'filterName', array('option1' => 'value1'));

        $this->assertEquals($filterDef, $this->object->getFilterDefinitions());
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterBuilder::bindRequest
     */
    public function testBindRequest()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
