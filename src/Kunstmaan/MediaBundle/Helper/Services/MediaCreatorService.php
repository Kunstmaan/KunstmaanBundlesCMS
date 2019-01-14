<?php

namespace Kunstmaan\MediaBundle\Helper\Services;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Service to easily add a media file to an existing media folder.
 * This is especially useful in migrations or places where you want to automate the uploading of media.
 *
 * Class MediaCreatorService
 */
class MediaCreatorService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var FolderRepository
     */
    protected $folderRepository;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->folderRepository = $this->em->getRepository('KunstmaanMediaBundle:Folder');
    }

    /**
     * @param $filePath string  The full filepath of the asset you want to upload. The filetype will be automatically detected.
     * @param $folderId integer For now you still have to manually pass the correct folder ID
     *
     * @return Media
     */
    public function createFile($filePath, $folderId)
    {
        $fileHandler = $this->container->get('kunstmaan_media.media_handlers.file');

        // Get file from FilePath.
        $data = new File($filePath, true);

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
