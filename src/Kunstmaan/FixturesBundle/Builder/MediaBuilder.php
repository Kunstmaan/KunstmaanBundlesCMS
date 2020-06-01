<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Mime\MimeTypes;

class MediaBuilder implements BuilderInterface
{
    private $em;

    private $fileHandler;

    /**
     * @var MimeTypes|MimeTypeGuesser
     */
    private $mimeTypeGuesser;

    private $folder;

    public function __construct(EntityManager $em, FileHandler $fileHandler, $mimeTypeGuesser)
    {
        $this->em = $em;
        $this->fileHandler = $fileHandler;
        $this->mimeTypeGuesser = $mimeTypeGuesser;
        if ($mimeTypeGuesser instanceof MimeTypeGuesserFactoryInterface) {
            $this->mimeTypeGuesser = $mimeTypeGuesser->get();
            @trigger_error('Passing a service instance of "\Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface" as the second argument is deprecated since KunstmaanMediaBundle 5.6 and will be replaced by the "@mime_types" service in KunstmaanMediaBundle 6.0. Inject the correct service instead.', E_USER_DEPRECATED);
        }
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
        $contentType = $this->guessMimeType($data->getPathname());

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

    private function guessMimeType($pathName)
    {
        if ($this->mimeTypeGuesser instanceof MimeTypeGuesser) {
            return $this->mimeTypeGuesser->guess($pathName);
        }

        return $this->mimeTypeGuesser->guessMimeType($pathName);
    }

    public function postFlushBuild(Fixture $fixture)
    {
        return;
    }
}
