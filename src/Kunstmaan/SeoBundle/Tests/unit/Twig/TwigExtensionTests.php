<?php

namespace Kunstmaan\SeoBundle\Tests\Entity;

use Kunstmaan\SeoBundle\Entity\Seo;
use Kunstmaan\SeoBundle\Twig\SeoTwigExtension;
use PHPUnit_Framework_TestCase;

/**
 * Class TwigExtensionTests
 * @package Tests\Kunstmaan\SeoBundle\Entity
 */
class TwigExtensionTests extends PHPUnit_Framework_TestCase
{
    protected $emMock;
    protected $entityMock;
    protected $seoRepoMock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->emMock = $this->createMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
    }

    /**
     * testShouldReturnNameForEntityWhenNoSEO
     */
    public function testShouldReturnNameForEntityWhenNoSEO()
    {
        $name = 'OK';

        $this->entityWithName($name);
        $this->noSeoFound();

        $object = new SeoTwigExtension($this->emMock);


        $result = $object->getTitleFor($this->entityMock);


        $this->assertEquals($name, $result);
    }

    /**
     * testShouldReturnNameForEntityWhenSEOWithTitleFound
     */
    public function testShouldReturnNameForEntityWhenSEOWithTitleFound()
    {
        $nokName = 'NOK';
        $name = 'OK';

        $this->entityWithName($nokName);
        $this->seoFoundWithTitle($name);

        $object = new SeoTwigExtension($this->emMock);


        $result = $object->getTitleFor($this->entityMock);


        $this->assertEquals($name, $result);
    }

    /**
     * @param string $name
     */
    protected function entityWithName($name)
    {
        $this->entityMock = $this->createMock('Kunstmaan\NodeBundle\Entity\AbstractPage');
        $this->entityMock->expects($this->once())->method('getTitle')->will($this->returnValue($name));
    }

    /**
     * NoSeoFound
     */
    protected function noSeoFound()
    {
        $this->ensureSeoRepoMock();
        $this->seoRepoMock->expects($this->once())
            ->method('findFor')
            ->will($this->returnValue(null));

        $this->wireUpSeoRepo();
    }

    /**
     * ensureSeoRepoMock
     */
    protected function ensureSeoRepoMock()
    {
        if (is_null($this->seoRepoMock)) {
            $this->seoRepoMock = $this->createMock('Kunstmaan\SeoBundle\Repository\SeoRepository', array(), array(), '', false);
        }
    }

    /**
     * wireUpSeoRepo
     */
    protected function wireUpSeoRepo()
    {
        $this->emMock->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('KunstmaanSeoBundle:Seo'))
            ->will($this->returnValue($this->seoRepoMock));
    }

    /**
     * @param string $title
     */
    protected function seoFoundWithTitle($title)
    {
        $this->ensureSeoRepoMock();

        $seoMock = new Seo();
        $seoMock->setRef($this->entityMock);
        $seoMock->setMetaTitle($title);

        $this->seoRepoMock->expects($this->once())
            ->method('findFor')
            ->will($this->returnValue($seoMock));

        $this->wireUpSeoRepo();
    }

}
