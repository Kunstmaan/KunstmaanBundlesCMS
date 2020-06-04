<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;
use Kunstmaan\MediaBundle\Model\ImageUploadModel;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * MediaManager
 */
class MediaManager
{
    /**
     * @var AbstractMediaHandler[]
     */
    protected $handlers = [];

    /**
     * @var AbstractMediaHandler
     */
    protected $defaultHandler;

    /** @var MimeTypesInterface */
    private $mimeTypes;

    /** @var ValidatorInterface */
    private $validator;

    /** @var array */
    private $imageExtensions;

    /** @var array */
    private $allowedExtensions;

    public function __construct(MimeTypesInterface $mimeTypes, ValidatorInterface $validator, array $imageExtesions, array $allowedExtensions)
    {
        $this->mimeTypes = $mimeTypes;
        $this->validator = $validator;
        $this->imageExtensions = $imageExtesions;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @param AbstractMediaHandler $handler Media handler
     *
     * @return MediaManager
     */
    public function addHandler(AbstractMediaHandler $handler)
    {
        $this->handlers[$handler->getName()] = $handler;

        return $this;
    }

    /**
     * @param AbstractMediaHandler $handler Media handler
     *
     * @return MediaManager
     */
    public function setDefaultHandler(AbstractMediaHandler $handler)
    {
        $this->defaultHandler = $handler;

        return $this;
    }

    /**
     * Returns handler with the highest priority to handle the Media item which can handle the item. If no handler is found, it returns FileHandler
     *
     * @param Media|File $media
     *
     * @return AbstractMediaHandler
     */
    public function getHandler($media)
    {
        $bestHandler = $this->defaultHandler;
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($media) && $handler->getPriority() > $bestHandler->getPriority()) {
                $bestHandler = $handler;
            }
        }

        return $bestHandler;
    }

    /**
     * Returns handler with the highest priority to handle the Media item based on the Type. If no handler is found, it returns FileHandler
     *
     * @param string $type
     *
     * @return AbstractMediaHandler
     */
    public function getHandlerForType($type)
    {
        $bestHandler = $this->defaultHandler;
        foreach ($this->handlers as $handler) {
            if ($handler->getType() == $type && $handler->getPriority() > $bestHandler->getPriority()) {
                $bestHandler = $handler;
            }
        }

        return $bestHandler;
    }

    /**
     * @return AbstractMediaHandler[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @return MediaManager
     */
    public function prepareMedia(Media $media)
    {
        $handler = $this->getHandler($media);
        $handler->prepareMedia($media);

        return $this;
    }

    /**
     * @param Media $media The media
     * @param bool  $new   Is new
     */
    public function saveMedia(Media $media, $new = false)
    {
        $handler = $this->getHandler($media);

        if ($new) {
            $handler->saveMedia($media);
        } else {
            $handler->updateMedia($media);
        }
    }

    public function removeMedia(Media $media)
    {
        $handler = $this->getHandler($media);
        $handler->removeMedia($media);
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function createNew($data)
    {
        if ($this->isExtensionAllowed($data)) {
            foreach ($this->handlers as $handler) {
                $result = $handler->createNew($data);
                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getFolderAddActions()
    {
        $result = [];
        foreach ($this->handlers as $handler) {
            $actions = $handler->getAddFolderActions();
            if ($actions) {
                $result = array_merge($actions, $result);
            }
        }

        return $result;
    }

    public function isExtensionAllowed(File $file): bool
    {
        $mimeType = $this->mimeTypes->guessMimeType($file->getRealPath());
        $extension = $this->mimeTypes->getExtensions($mimeType)[0] ?? 'not_allowed';
        if (!in_array($extension, $this->allowedExtensions, true)) {
            return false;
        }

        if (!in_array($extension, $this->imageExtensions, true)) {
            return true;
        }

        $fileUploadModel = new ImageUploadModel($file);
        $errors = $this->validator->validate($fileUploadModel);

        return 0 === $errors->count();
    }
}
