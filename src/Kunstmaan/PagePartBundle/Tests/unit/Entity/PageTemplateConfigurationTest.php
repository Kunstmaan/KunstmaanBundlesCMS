<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use PHPUnit\Framework\TestCase;

class PageTemplateConfigurationTest extends TestCase
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

        $testNode = $this->createMock(AbstractPage::class);

        $repo->expects($this->any())
            ->method('find')
            ->willReturn($testNode);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($repo);

        $this->assertInstanceOf(\get_class($testNode), $config->getPage($em));
    }
}
