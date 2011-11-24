<?php

namespace Kunstmaan\MediaBundle\Helper;

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