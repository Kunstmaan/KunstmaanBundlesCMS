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
        $pageParts = [['name' => 'Header', 'class' => 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'],
                           ['name' => 'Text', 'class' => 'Kunstmaan\PagePartBundle\Entity\TextPagePart'],
                           ['name' => 'Link', 'class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart'],
                           ['name' => 'Raw HTML', 'class' => 'Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart'],
                           ['name' => 'Line', 'class' => 'Kunstmaan\PagePartBundle\Entity\LinePagePart'],
                           ['name' => 'TOC', 'class' => 'Kunstmaan\PagePartBundle\Entity\TocPagePart'],
                           ['name' => 'Link To Top', 'class' => 'Kunstmaan\PagePartBundle\Entity\ToTopPagePart'], ];

        return $pageParts;
    }
}
