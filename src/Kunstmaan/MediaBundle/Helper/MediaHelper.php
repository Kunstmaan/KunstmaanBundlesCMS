<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;

use Symfony\Component\HttpFoundation\File\File as SystemFile;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * MediaHelper
 */
class MediaHelper
{

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var string
     */
    protected $path;

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;
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
        $path     = sys_get_temp_dir() . "/" . $filename;
        $savefile = fopen($path, 'w');

        $this->path = $path;

        curl_setopt($ch, CURLOPT_FILE, $savefile);
        curl_exec($ch);
        curl_close($ch);
        chmod($path, 777);

        $upload = new SystemFile($path);

        fclose($savefile);

        $this->setMedia($upload);

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