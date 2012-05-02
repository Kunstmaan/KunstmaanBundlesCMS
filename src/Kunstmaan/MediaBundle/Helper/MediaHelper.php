<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\HttpFoundation\File\File as SystemFile;

class MediaHelper{

    protected $media;
    protected $path;

    public function getMedia(){
        return $this->media;
    }

    public function setMedia($media){
            $this->media = $media;
    }

    public function getMediaFromUrl($mediaurl)
    {
        $ch = curl_init($mediaurl);
        $url = parse_url($mediaurl);
        $info = pathinfo($url['path']);
        $filename = $info['filename'].".".$info['extension'];
        $path = sys_get_temp_dir()."/".$filename;
        $savefile = fopen($path, 'w');

        $this->path = $path;

        curl_setopt($ch, CURLOPT_FILE, $savefile);
        curl_exec($ch);
        curl_close($ch);
        chmod($path, 777);

        $upload = new SystemFile($path);

        fclose($savefile);

        $this->setMedia( $upload );

        if ($this->getMedia()==null) {
            unlink($path);
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException("can't link file");
        }
    }

    public function __destruct()
    {
        if($this->path!=null){
            unlink($this->path);
        }
    }

}

?>