<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * MediaManager
 */
class MediaManager
{
    /**
     * @var AbstractMediaHandler[]
     */
    protected $handlers = array();

    /**
     * @param AbstractMediaHandler $handler Media handler
     *
     * @return void
     */
    public function addHandler(AbstractMediaHandler $handler)
    {
        $this->handlers[$handler->getName()] = $handler;
    }

    /**
     * @param Media $media
     *
     * @return AbstractMediaHandler
     *
     * @throws \InvalidArgumentException when there is no context for this name
     */
    public function getHandler(Media $media)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($media)) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException(sprintf('Handler "%s" doesn\'t exist', $media->getContentType()));
    }

    /**
     * @param string $type
     *
     * @return AbstractMediaHandler
     *
     * @throws \InvalidArgumentException
     */
    public function getHandlerForType($type)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->getType() == $type) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException(sprintf('Handler "%s" doesn\'t exist', $type));
    }

    /**
     * @return AbstractMediaHandler[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function prepareMedia(Media $media)
    {
        $handler = $this->getHandler($media);
        $handler->prepareMedia($media);
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

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function removeMedia(Media $media)
    {
        $handler = $this->getHandler($media);
        $handler->removeMedia($media);
    }

    /**
     * @param mixed $data
     *
     * @return Media
     */
    public function createNew($data)
    {
        foreach ($this->handlers as $handler) {
            $result = $handler->createNew($data);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getFolderAddActions()
    {
        $result = array();
        foreach ($this->handlers as $handler) {
            $actions = $handler->getAddFolderActions();
            if ($actions) {
                $result = array_merge($actions, $result);
            }
        }

        return $result;
    }
}