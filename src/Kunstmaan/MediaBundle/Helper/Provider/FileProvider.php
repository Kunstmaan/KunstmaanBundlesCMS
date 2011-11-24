<?php

namespace Kunstmaan\KMediaBundle\Helper\Provider;

use Kunstmaan\KMediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Kunstmaan\KMediaBundle\Helper\Generator\ExtensionGuesser;

class FileProvider extends AbstractProvider
{
    /* @var string */
    protected $template = null;

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

        if (!$content instanceof File) {
            if (!is_file($content)) {
                throw new \RuntimeException('Invalid file');
            }

            $media->setContent(new File($content));
        }

        $metadata = array();

        $media->setMetadata($metadata);
        //$media->setName($media->getContent()->getBasename());
        $media->setContentType($media->getContent()->getMimeType());
    }

    /**
     * {@inheritDoc}
     */
    public function saveMedia(Media $media)
    {
        if (!$media->getContent() instanceof File) {
            return;
        }

        $originalFile = $this->getOriginalFile($media);
        $originalFile->setContent(file_get_contents($media->getContent()->getRealPath()));
    }

    /**
     * {@inheritDoc}
     */
    public function removeMedia(Media $media)
    {
        foreach($this->formats as $format => $options) {
            $path = $this->generateRelativePath($media, $format);
            if ($this->getFilesystem()->has($path)) {
                $this->getFilesystem()->delete($path);
            }
        }

        // Original
        $path = $this->getOriginalFilePath($media);
        if ($this->getFilesystem()->has($path)) {
            $this->getFilesystem()->delete($path);
        }
    }

    public function getOriginalFilePath(Media $media)
    {
        return sprintf(
            '%s/%s.%s',
            $this->generatePath($media),
            $media->getUuid(),
            ExtensionGuesser::guess($media->getContentType())
        );
    }

    public function getOriginalFile(Media $media)
    {
        return $this->getFilesystem()->get($this->getOriginalFilePath($media), true);
    }

    /**
     * {@inheritDoc}
     */
    public function getMediaUrl(Media $media, $format = null)
    {
        // wants original file
        if (null == $format) {
            $path = $this->getOriginalFilePath($media);
        }
        else {
            $path = $this->generateRelativePath($media, $format);
        }

        return $this->cdn->getFullPath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function getRenderOptions(Media $media, $format, array $options = array())
    {
        return $options;
    }
}