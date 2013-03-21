<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Kunstmaan\MediaBundle\Entity\Media;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * FileHelper
 */
class FileHelper
{

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->media->getName();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->media->setName($name);
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        $this->media->setContent($file);
        $this->media->setContentType($file->getMimeType());
        $this->media->setUrl('/uploads/media/'.$this->media->getUuid() . '.' . $this->media->getContent()->getExtension());
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param string $mediaurl
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
     */
    public function getMediaFromUrl($mediaurl)
    {
        $path = tempnam(sys_get_temp_dir(), 'kuma_');
        $saveFile = fopen($path, 'w');
        $this->path = $path;

        $ch = curl_init($mediaurl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $saveFile);
        curl_exec($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        fclose($saveFile);
        chmod($path, 0777);

        $url      = parse_url($effectiveUrl);
        $info     = pathinfo($url['path']);
        $filename = $info['filename'] . "." . $info['extension'];

        $upload = new UploadedFile($path, $filename);
        $this->getMedia()->setContent($upload);

        if ($this->getMedia() == null) {
            unlink($path);
            throw new AccessDeniedException("can't link file");
        }
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        if ($this->path != null) {
            unlink($this->path);
        }
    }

}