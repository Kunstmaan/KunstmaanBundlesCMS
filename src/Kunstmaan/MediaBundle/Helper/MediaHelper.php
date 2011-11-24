<?php

namespace Kunstmaan\KMediaBundle\Helper;

/**
 * Comment controller.
 */
class MediaHelper{

    protected $media;

    public function getMedia(){
        return $this->media;
    }

    public function setMedia($media){
            $this->media = $media;
        }

}

?>