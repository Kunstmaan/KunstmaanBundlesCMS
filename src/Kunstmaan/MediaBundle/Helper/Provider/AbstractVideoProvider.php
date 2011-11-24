<?php

namespace Kunstmaan\KMediaBundle\Helper\Provider;

use Kunstmaan\KMediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;
use Kunstmaan\KMediaBundle\Helper\Generator\ExtensionGuesser;

abstract class AbstractVideoProvider extends AbstractProvider
{
    abstract protected function getMetadata(Media $media);

    /**
     * {@inheritDoc}
     */
    public function prepareMedia(Media $media)
    {
        if (null == $media->getUuid()) {
            $uuid = $this->uuidGenerator->generateUuid($media);
            $media->setUuid($uuid);
        }

        $content = $media->getContent();
        if (empty($content)) {
            return;
        }

        $media->setContentType('video/x-flv');
        $media->setUpdatedAt(new \DateTime());
    }

    /**
     * {@inheritDoc}
     */
    public function saveMedia(Media $media)
    {
        // Nothing to do
    }

    /**
     * {@inheritDoc}
     */
    public function updateMedia(Media $media)
    {
        $this->saveMedia($media);
    }


    /**
     * {@inheritDoc}
     */
    public function removeMedia(Media $media)
    {
        // Nothing to do
    }

    /**
     * {@inheritDoc}
     */
    public function getMediaUrl(Media $media, $format = null)
    {
        $path = $this->generateRelativePath($media, $format);

        return $this->cdn->getFullPath($path);
    }
}