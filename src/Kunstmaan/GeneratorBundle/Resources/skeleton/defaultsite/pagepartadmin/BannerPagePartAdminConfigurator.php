<?php

namespace {{ namespace }}\PagePartAdmin;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

class BannerPagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{

    protected $pagePartTypes = array();

    public function __construct($pagepartProviders = array())
    {
        $this->pagePartTypes = array(
            array('name' => 'Header', 'class'=> 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'),
            array('name' => 'Text', 'class'=> 'Kunstmaan\PagePartBundle\Entity\TextPagePart'),
            array('name' => 'Line', 'class'=> 'Kunstmaan\PagePartBundle\Entity\LinePagePart'),
            array('name' => 'Image', 'class'=> 'Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart'),
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
        return "Banners";
    }

    /**
     * {@inheritdoc}
     */
    function getDefaultContext()
    {
        return "banners";
    }
}
