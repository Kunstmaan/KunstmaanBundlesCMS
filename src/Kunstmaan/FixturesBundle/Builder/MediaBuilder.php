<?php

namespace Kunstmaan\FixturesBundle\Builder;


use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class MediaBuilder implements BuilderInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var FileHandler */
    private $fileHandler;

    /**
     * @var MimeTypeGuesserInterface
     */
    private $mimeTypeGuesser;

    private $folder;

    /**
     * MediaBuilder constructor.
     *
     * @param EntityManagerInterface          $em
     * @param FileHandler                     $fileHandler
     * @param MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory
     */
    public function __construct(EntityManagerInterface $em, FileHandler $fileHandler, MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory)
    {
        $this->em = $em;
        $this->fileHandler = $fileHandler;
        $this->mimeTypeGuesser = $mimeTypeGuesserFactory->get();
    }

    public function canBuild(Fixture $fixture)
    {
        if ($fixture->getEntity() instanceof Media) {
            return true;
        }

        return false;
    }

    public function preBuild(Fixture $fixture)
    {
        $properties = $fixture->getProperties();
        if (!isset($properties['folder'])) {
            throw new \Exception('There is no folder specified for media fixture '.$fixture->getName());
        }

        $this->folder = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(['rel' => $properties['folder']]);

        if (!$this->folder instanceof Folder) {
            $this->folder = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(['internalName' => $properties['folder']]);
        }

        if (!$this->folder instanceof Folder) {
            throw new \Exception('Could not find the specified folder for media fixture '.$fixture->getName());
        }


    }

    public function postBuild(Fixture $fixture)
    {
        /** @var Media $media */
        $media = $fixture->getEntity();

        $filePath = $media->getOriginalFilename();
        $data = new File($filePath, true);
        $contentType = $this->mimeTypeGuesser->guess($data->getPathname());

        if (method_exists($data, 'getClientOriginalName')) {
            $media->setOriginalFilename($data->getClientOriginalName());
        } else {
            $media->setOriginalFilename($data->getFilename());
        }

        if ($media->getName() === null) {
            $media->setName($media->getOriginalFilename());
        }

        $media->setContent($data);

        $media->setContentType($contentType);
        $media->setFolder($this->folder);

        $this->fileHandler->prepareMedia($media);
        $this->fileHandler->updateMedia($media);
        $this->fileHandler->saveMedia($media);
    }

    public function postFlushBuild(Fixture $fixture)
    {
        return;
    }
}
