<?php

namespace Kunstmaan\MediaBundle\Tests\Helper;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Helper\FolderManager;
use PHPUnit\Framework\TestCase;

class FolderManagerTest extends TestCase
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @var FolderManager
     */
    protected $object;

    /**
     * @var array
     */
    private $parents;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder('Kunstmaan\MediaBundle\Repository\FolderRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository
            ->expects($this->any())
            ->method('getParentIds')
            ->willReturn([1, 2]);

        $folder1 = new Folder();
        $folder1->setId(1);

        $folder2 = new Folder();
        $folder2->setId(2);

        $this->parents = [$folder1, $folder2];

        $this->repository
            ->expects($this->any())
            ->method('getPath')
            ->willReturn([$folder1, $folder2]);

        $rootFolder = new Folder();
        $rootFolder->setId(1);

        $this->repository
            ->expects($this->any())
            ->method('getFolder')
            ->with($this->equalTo(1))
            ->willReturn($rootFolder);

        $this->folder = new Folder();
        $this->folder->setId(3);

        $this->object = new FolderManager($this->repository);
    }

    public function testGetFolderHierarchy()
    {
        $this->repository
            ->expects($this->once())
            ->method('childrenHierarchy')
            ->with($this->equalTo($this->folder))
            ->willReturn([]);

        $this->object->getFolderHierarchy($this->folder);
    }

    public function testGetRootFolderFor()
    {
        $this->repository
            ->expects($this->once())
            ->method('getFolder')
            ->with($this->equalTo(1));

        $rootFolder = $this->object->getRootFolderFor($this->folder);
        $this->assertEquals(1, $rootFolder->getId());
    }

    public function testGetParentIds()
    {
        $this->repository
            ->expects($this->once())
            ->method('getParentIds')
            ->with($this->equalTo($this->folder));

        $this->assertEquals([1, 2], $this->object->getParentIds($this->folder));
    }

    public function testGetParents()
    {
        $this->repository
            ->expects($this->once())
            ->method('getPath')
            ->with($this->equalTo($this->folder));

        $this->assertEquals($this->parents, $this->object->getParents($this->folder));
    }
}
