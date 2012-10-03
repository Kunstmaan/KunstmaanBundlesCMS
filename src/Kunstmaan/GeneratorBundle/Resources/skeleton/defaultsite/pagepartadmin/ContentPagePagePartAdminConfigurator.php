<?php

namespace {{ namespace }}\PagePartAdmin;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

class ContentPagePagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{

    protected $pagePartTypes = array();

    public function __construct($pagepartProviders = array())
    {
        $this->pagePartTypes = array(
            array('name' => 'Header', 'class'=> 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'),
            array('name' => 'Text', 'class'=> 'Kunstmaan\PagePartBundle\Entity\TextPagePart'),
            array('name' => 'Line', 'class'=> 'Kunstmaan\PagePartBundle\Entity\LinePagePart'),
            array('name' => 'TOC', 'class'=> 'Kunstmaan\PagePartBundle\Entity\TocPagePart'),
            array('name' => 'Link', 'class'=> 'Kunstmaan\PagePartBundle\Entity\LinkPagePart'),
            array('name' => 'To Top', 'class'=> 'Kunstmaan\PagePartBundle\Entity\ToTopPagePart'),
            array('name' => 'Image', 'class'=> 'Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart'),
            array('name' => 'Download', 'class'=> 'Kunstmaan\MediaPagePartBundle\Entity\DownloadPagePart'),
            array('name' => 'Slide', 'class'=> 'Kunstmaan\MediaPagePartBundle\Entity\SlidePagePart'),
            array('name' => 'Video', 'class'=> 'Kunstmaan\MediaPagePartBundle\Entity\VideoPagePart'),
        );
    }

    /**
     * {@inheritdoc}
     */
    function getPossiblePagePartTypes()
    {
        return $this->pagePartTypes;
    }

    /**
     * {@inheritdoc}
     */
    function getName()
    {
        return "Pageparts";
    }

    /**
     * {@inheritdoc}
     */
    function getDefaultContext()
    {
        return "main";
    }
}
