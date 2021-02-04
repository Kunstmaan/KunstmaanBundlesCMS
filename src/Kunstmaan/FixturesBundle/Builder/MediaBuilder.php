<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
use Symfony\Component\Mime\MimeTypes;

class MediaBuilder implements BuilderInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var FileHandler */
    private $fileHandler;

    /** @var MimeTypes|ExtensionGuesserInterface */
    private $mimeTypeGuesser;

    private $folder;

    public function __construct(EntityManager $em, FileHandler $fileHandler, /* MimeTypes */ $mimeTypeGuesser)
    {
        $this->em = $em;
        $this->fileHandler = $fileHandler;
        $this->mimeTypeGuesser = $mimeTypeGuesser;
        if ($mimeTypeGuesser instanceof MimeTypeGuesserFactoryInterface) {
            @trigger_error(sprintf('Passing an instance of "%s" for the "$mimeTypeGuesser" parameter is deprecated since KunstmaanMediaBundle 5.7 and will be replaced by the "@mime_types" service in KunstmaanMediaBundle 6.0. Inject the correct service instead.', MimeTypeGuesserFactoryInterface::class), E_USER_DEPRECATED);

            $this->mimeTypeGuesser = $mimeTypeGuesser->get();
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
            throw new \Exception('There is no folder specified for media fixture ' . $fixture->getName());
        }

        $this->folder = $this->em->getRepository(Folder::class)->findOneBy(['rel' => $properties['folder']]);

        if (!$this->folder instanceof Folder) {
            $this->folder = $this->em->getRepository(Folder::class)->findOneBy(['internalName' => $properties['folder']]);
        }

        if (!$this->folder instanceof Folder) {
            throw new \Exception('Could not find the specified folder for media fixture ' . $fixture->getName());
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

    private function guessMimeType($pathName): ?string
    {
        if ($this->mimeTypeGuesser instanceof MimeTypeGuesserInterface) {
            return $this->mimeTypeGuesser->guess($pathName);
        }

        return $this->mimeTypeGuesser->guessMimeType($pathName);
    }

    public function postFlushBuild(Fixture $fixture)
    {
        return;
    }
}
