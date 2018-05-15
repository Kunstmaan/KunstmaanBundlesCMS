<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Kunstmaan\NodeBundle\Tests\Entity\TestNode;
use PHPUnit_Framework_TestCase;

/**
 * Class LinkPagePartTest
 * @package Tests\Kunstmaan\PagePartBundle\Tests\Entity
 */
class PageTemplateConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $config = new PageTemplateConfiguration();
        $config->setPageId(5);
        $config->setPageEntityName(PageTemplate::class);
        $config->setPageTemplate('string!');

        $this->assertEquals(5, $config->getPageId());
        $this->assertEquals(PageTemplate::class, $config->getPageEntityName());
        $this->assertEquals('string!', $config->getPageTemplate());

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->any())
            ->method('find')
            ->will($this->returnValue(new TestNode()));

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $this->assertInstanceOf(TestNode::class, $config->getPage($em));
    }
}
