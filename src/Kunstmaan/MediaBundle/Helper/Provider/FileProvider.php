<?php

namespace Kunstmaan\MediaBundle\Helper\Provider;

use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Kunstmaan\MediaBundle\Helper\Generator\ExtensionGuesser;

/**
 * FileProvider
 */
class FileProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $template = null;

    /**
     * @param Media $media
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

            $file = new File($content);
            $media->setContent($file);
        }

        $metadata = array();

        $media->setFileSize(filesize($media->getContent()));
        $media->setMetadata($metadata);
        //$media->setName($media->getContent()->getBasename());
        $media->setContentType($media->getContent()->getMimeType());
    }

    /**
     * @param Media $media
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
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
        foreach ($this->formats as $format => $options) {
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

    /**
     * @param Media $media
     *
     * @return string
     */
    public function getOriginalFilePath(Media $media)
    {
        return sprintf('%s/%s.%s', $this->generatePath($media), $media->getUuid(), ExtensionGuesser::guess($media->getContentType()));
    }

    /**
     * @param Media $media
     *
     * @return \Gaufrette\File
     */
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
        } else {
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