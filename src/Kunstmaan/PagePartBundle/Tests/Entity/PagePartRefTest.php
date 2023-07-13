<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use PHPUnit\Framework\TestCase;

class PagePartRefTest extends TestCase
{
    public function testGetSet()
    {
        $part = new PagePartRef();
        $part->setId(1);
        $part->setPageId(2);
        $part->setPageEntityname(PagePartRef::class);
        $part->setCreated(new \DateTime());
        $part->setUpdated(new \DateTime());
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

        $repo
            ->method('find')
            ->willReturn(new PagePart());

        $em
            ->method('getRepository')
            ->willReturn($repo);

        $this->assertSame(1, $part->getId());
        $this->assertSame(2, $part->getPageId());
        $this->assertSame(PagePartRef::class, $part->getPageEntityname());
        $this->assertInstanceOf(\DateTime::class, $part->getCreated());
        $this->assertInstanceOf(\DateTime::class, $part->getUpdated());
        $this->assertSame('a string', $part->getContext());
        $this->assertSame(3, $part->getSequencenumber());
        $this->assertSame('4', $part->getPagePartId());
        $this->assertSame(PagePartRef::class, $part->getPagePartEntityname());
        $this->assertSame('pagepartref in context a string', $part->__toString());
        $this->assertInstanceOf(PagePart::class, $part->getPagePart($em));
    }
}
