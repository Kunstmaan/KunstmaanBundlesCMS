<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use PHPUnit\Framework\TestCase;

class PagePart extends AbstractPagePart
{
    public function getDefaultView()
    {
    }

    public function getDefaultAdminType()
    {
    }
}

class AbstractPagePartTest extends TestCase
{
    public function testGetViews()
    {
        $part = new PagePart();
        $this->assertSame('', $part->getAdminView());
        $this->assertSame('', $part->getView());
    }
}
