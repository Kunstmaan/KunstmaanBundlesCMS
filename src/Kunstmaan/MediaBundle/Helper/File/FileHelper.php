<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @return Folder
     */
    public function getFolder()
    {
        return $this->media->getFolder();
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder)
    {
        $this->media->setFolder($folder);
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->media->getCopyright();
    }

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright)
    {
        $this->media->setCopyright($copyright);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->media->getDescription();
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->media->setDescription($description);
    }

    public function getOriginalFilename()
    {
        return $this->media->getOriginalFilename();
    }

    /**
     * @param string $name
     */
    public function setOriginalFilename($name)
    {
        $this->media->setOriginalFilename($name);
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
        $this->media->setUrl(
            '/uploads/media/' . $this->media->getUuid() . '.' . $this->media->getContent()->getExtension()
        );
    }

    /**
     * @param string $mediaUrl
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
     */
    public function getMediaFromUrl($mediaUrl)
    {
        $path = tempnam(sys_get_temp_dir(), 'kuma_');
        $saveFile = fopen($path, 'w');
        $this->path = $path;

        $ch = curl_init($mediaUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $saveFile);
        curl_exec($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        fclose($saveFile);
        chmod($path, 0777);

        $url = parse_url($effectiveUrl);
        $info = pathinfo($url['path']);
        $filename = $info['filename'] . "." . $info['extension'];

        $upload = new UploadedFile($path, $filename);
        $this->getMedia()->setContent($upload);

        if ($this->getMedia() === null) {
            unlink($path);
            throw new AccessDeniedException("Can not link file");
        }
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        if ($this->path !== null) {
            unlink($this->path);
        }
    }
}
