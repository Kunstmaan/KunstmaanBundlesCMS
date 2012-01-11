<?php

namespace Kunstmaan\MediaBundle\PagePartAdmin;

class Builder {

    public function getPageParts() {
        $pageParts = array(
            array('name' => 'Image', 'class'=>'Kunstmaan\MediaBundle\Entity\ImagePagePart')
        );

        return $pageParts;
    }
}