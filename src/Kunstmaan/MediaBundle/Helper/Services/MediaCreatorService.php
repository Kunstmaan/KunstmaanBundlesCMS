<?php

namespace Kunstmaan\MediaBundle\Helper\Services;

use Doctrine\ORM\EntityManager;

use Gaufrette\Filesystem;

use Kunstmaan\MediaBundle\Entity\Folder,
    Kunstmaan\MediaBundle\Repository\FolderRepository,
    Kunstmaan\MediaBundle\Entity\Media,
    Kunstmaan\MediaBundle\Helper\File\FileHandler;

use Symfony\Component\HttpFoundation\File\File;

// TODO: Would be cool if we could pass on the folder name. Or the path with a locale.
// TODO: Needs severe cleanup where the filesystem is not manipulated. But how do you detect the context of a running process?
//       Also, the FileHandler would be a better place to put that logic.
// TODO: Write tests for this. Once called as a command, once as an action on a controller.
// TODO: Fix bug where the web context also writes a file in the root /uploads/media in addition to web/uploads/media.

/**
 * Service to easily add a media file to an existing media folder.
 * This is especially useful in migrations or places where you want to automate the uploading of media.
 *
 * Class MediaCreatorService
 * @package Kunstmaan\MediaBundle\Helper\Services
 */
class MediaCreatorService
{

    /** @var EntityManager */
    protected $em;
    /** @var FolderRepository */
    protected $folderRepository;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        $this->folderRepository = $em->getRepository('KunstmaanMediaBundle:Folder');
    }

    const CONTEXT_CONSOLE = 'console';
    const CONTEXT_WEB = 'web';

    /**
     * @param $filePath string  The full filepath of the asset you want to upload. The filetype will be automatically detected.
     * @param $folderId integer For now you still have to manually pass the correct folder ID.
     * @param string $context   This is needed because the Filesystem basepath differs between web & console application env.
     *
     * @return Media
     */
    public function createFile($filePath, $folderId, $context = MediaCreatorService::CONTEXT_WEB)
    {
        $fileHandler = new FileHandler();

        // Get file from FilePath.
        $data = new File($filePath, true);

        if ($context == MediaCreatorService::CONTEXT_CONSOLE) {
            $fileHandler->fileSystem = new Filesystem(new \Gaufrette\Adapter\Local("web/uploads/media/", true));
        }

        /** @var $media Media */
        $media = $fileHandler->createNew($data);
        /** @var $folder Folder */
        $folder = $this->folderRepository->getFolder($folderId);

        $media->setFolder($folder);

        $fileHandler->prepareMedia($media);
        $fileHandler->updateMedia($media);
        $fileHandler->saveMedia($media);

        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }
}