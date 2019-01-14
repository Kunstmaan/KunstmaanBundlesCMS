<?php

namespace Kunstmaan\SiteMapBundle\Tests\Entity;

use Kunstmaan\SitemapBundle\Entity\SitemapPage;
use PHPUnit_Framework_TestCase;

/**
 * Class RobotsTest
 */
class SiteMapPageTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $object = new SitemapPage();

        $this->assertEquals('KunstmaanSitemapBundle:SitemapPage:view.html.twig', $object->getDefaultView());
        $this->assertTrue($object->isHiddenFromSitemap());
        $this->assertTrue($object->isChildrenHiddenFromSitemap());
        $this->assertTrue(is_array($object->getPossibleChildTypes()));
        $this->assertTrue(is_array($object->getPagePartAdminConfigurations()));
    }
}
