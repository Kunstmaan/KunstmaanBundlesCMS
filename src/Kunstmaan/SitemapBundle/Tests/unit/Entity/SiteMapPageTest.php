<?php

namespace Kunstmaan\SiteMapBundle\Tests\Entity;

use Kunstmaan\SitemapBundle\Entity\SitemapPage;
use PHPUnit\Framework\TestCase;

/**
 * Class RobotsTest
 */
class SiteMapPageTest extends TestCase
{
    public function testGetters()
    {
        $object = new SitemapPage();

        $this->assertEquals('@KunstmaanSitemap/SitemapPage/view.html.twig', $object->getDefaultView());
        $this->assertTrue($object->isHiddenFromSitemap());
        $this->assertTrue($object->isChildrenHiddenFromSitemap());
        $this->assertTrue(is_array($object->getPossibleChildTypes()));
        $this->assertTrue(is_array($object->getPagePartAdminConfigurations()));
    }
}
