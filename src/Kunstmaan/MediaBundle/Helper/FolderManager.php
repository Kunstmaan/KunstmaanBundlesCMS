<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Repository\FolderRepository;

class FolderManager
{
    /** @var \Kunstmaan\MediaBundle\Repository\FolderRepository $repository */
    private $repository;

    /**
     * @var \Kunstmaan\MediaBundle\Repository\FolderRepository $repository
     */
    public function __construct(FolderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Folder $rootFolder
     *
     * @return array|string
     */
    public function getFolderHierarchy(Folder $rootFolder)
    {
        return $this->repository->childrenHierarchy($rootFolder);
    }

    /**
     * @param Folder $folder
     *
     * @return Folder
     */
    public function getRootFolderFor(Folder $folder)
    {
        $parentIds = $this->getParentIds($folder);

        return $this->repository->getFolder($parentIds[0]);
    }

    /**
     * @param Folder $folder
     *
     * @return array
     */
    public function getParentIds(Folder $folder)
    {
        return $this->repository->getParentIds($folder);
    }

    /**
     * @param Folder $folder
     *
     * @return array
     */
    public function getParents(Folder $folder)
    {
        return $this->repository->getPath($folder);
    }
}
