<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

/**
 * Builder
 */
class Builder
{
    /**
     * @return array
     */
    public function getPageParts()
    {
        $pageParts = array(array('name' => 'Header', 'class' => 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'),
                           array('name' => 'Text', 'class' => 'Kunstmaan\PagePartBundle\Entity\TextPagePart'),
                           array('name' => 'Link', 'class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart'),
                           array('name' => 'Raw HTML', 'class' => 'Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart'),
                           array('name' => 'Line', 'class' => 'Kunstmaan\PagePartBundle\Entity\LinePagePart'),
                           array('name' => 'TOC', 'class' => 'Kunstmaan\PagePartBundle\Entity\TocPagePart'),
                           array('name' => 'Link To Top', 'class' => 'Kunstmaan\PagePartBundle\Entity\ToTopPagePart'), );

        return $pageParts;
    }
}
