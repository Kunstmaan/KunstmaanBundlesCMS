<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\AbstractORMFilterType;
use Doctrine\ORM\QueryBuilder;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-26 at 13:21:33.
 */
class AbstractORMFilterTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\AbstractORMFilterType<extended>
     */
    public function testSetQueryBuilder()
    {
        /* @var AbstractORMFilterType $object */
        $object = $this->getMockForAbstractClass('Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\AbstractORMFilterType', array('column', 'alias'));

        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $object->setQueryBuilder($queryBuilder);
    }
}
