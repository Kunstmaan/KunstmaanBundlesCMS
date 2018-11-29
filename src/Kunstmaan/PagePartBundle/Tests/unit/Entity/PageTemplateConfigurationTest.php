<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Codeception\Stub;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use PHPUnit_Framework_TestCase;

/**
 * Class LinkPagePartTest
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

        $testNode = Stub::makeEmpty(AbstractPage::class);

        $repo->expects($this->any())
            ->method('find')
            ->will($this->returnValue($testNode));

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $this->assertInstanceOf(get_class($testNode), $config->getPage($em));
    }
}
