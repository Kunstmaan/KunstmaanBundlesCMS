<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

class Builder {

    public function getPageParts() {
        $pageParts = array(
            array('name' => 'Header',   'class'=>'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'),
            array('name' => 'Text',     'class'=>'Kunstmaan\PagePartBundle\Entity\TextPagePart'),
            array('name' => 'Line',     'class'=>'Kunstmaan\PagePartBundle\Entity\LinePagePart'),
            array('name' => 'TOC',      'class'=>'Kunstmaan\PagePartBundle\Entity\TocPagePart'),
        );

        return $pageParts;
    }
}