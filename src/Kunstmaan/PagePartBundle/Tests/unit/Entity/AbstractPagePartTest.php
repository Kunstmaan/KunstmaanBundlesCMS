<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use PHPUnit_Framework_TestCase;

class PagePart extends AbstractPagePart
{
    public function getDefaultView()
    {
    }

    public function getDefaultAdminType()
    {
    }
}

/**
 * Class AbstractPagePartTest
 */
class AbstractPagePartTest extends PHPUnit_Framework_TestCase
{
    public function testGetViews()
    {
        $part = new PagePart();
        $this->assertEquals('', $part->getAdminView());
        $this->assertEquals('', $part->getView());
    }
}
