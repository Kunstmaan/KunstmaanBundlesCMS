<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use PHPUnit_Framework_TestCase;

/**
 * Class PagePartRefTest
 */
class PagePartRefTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $part = new PagePartRef();
        $part->setId(1);
        $part->setPageId(2);
        $part->setPageEntityname(PagePartRef::class);
        $part->setCreated(new DateTime());
        $part->setUpdated(new DateTime());
        $part->setUpdatedValue();
        $part->setContext('a string');
        $part->setSequencenumber(3);
        $part->setPagePartId('4');
        $part->setPagePartEntityname(PagePartRef::class);

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->any())
            ->method('find')
            ->will($this->returnValue(new PagePart()));

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $this->assertEquals(1, $part->getId());
        $this->assertEquals(2, $part->getPageId());
        $this->assertEquals(PagePartRef::class, $part->getPageEntityname());
        $this->assertInstanceOf(DateTime::class, $part->getCreated());
        $this->assertInstanceOf(DateTime::class, $part->getUpdated());
        $this->assertEquals('a string', $part->getContext());
        $this->assertEquals(3, $part->getSequencenumber());
        $this->assertEquals('4', $part->getPagePartId());
        $this->assertEquals(PagePartRef::class, $part->getPagePartEntityname());
        $this->assertEquals('pagepartref in context a string', $part->__toString());
        $this->assertInstanceOf(PagePart::class, $part->getPagePart($em));
    }
}
