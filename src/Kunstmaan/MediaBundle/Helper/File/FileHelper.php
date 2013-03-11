<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Kunstmaan\MediaBundle\Entity\Media;

use Symfony\Component\HttpFoundation\File\File as SystemFile;
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
        $ch       = curl_init($mediaurl);
        $url      = parse_url($mediaurl);
        $info     = pathinfo($url['path']);
        $filename = $info['filename'] . "." . $info['extension'];
        $path     = rtrim(sys_get_temp_dir(), '/') . '/' . $filename;

        $saveFile = fopen($path, 'w');
        $this->path = $path;

        curl_setopt($ch, CURLOPT_FILE, $saveFile);
        curl_exec($ch);
        curl_close($ch);
        chmod($path, 777);

        $upload = new SystemFile($path);

        fclose($saveFile);

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