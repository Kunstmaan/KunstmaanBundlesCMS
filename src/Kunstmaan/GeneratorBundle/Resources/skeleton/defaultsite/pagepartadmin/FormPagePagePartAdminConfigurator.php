<?php

namespace {{ namespace }}\PagePartAdmin;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

class FormPagePagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{

    protected $pagePartTypes = array();

    public function __construct($pagepartProviders = array())
    {
        $this->pagePartTypes = array(
            array('name' => 'Single Line text input', 'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart'),
            array('name' => 'Multi Line text input', 'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart'),
            array('name' => 'Choice input', 'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart'),
            array('name' => 'Header', 'class'=> 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'),
            array('name' => 'Text', 'class'=> 'Kunstmaan\PagePartBundle\Entity\TextPagePart'),
            array('name' => 'Line', 'class'=> 'Kunstmaan\PagePartBundle\Entity\LinePagePart'),
            array('name' => 'TOC', 'class'=> 'Kunstmaan\PagePartBundle\Entity\TocPagePart'),
            array('name' => 'Link', 'class'=> 'Kunstmaan\PagePartBundle\Entity\LinkPagePart'),
            array('name' => 'To Top', 'class'=> 'Kunstmaan\PagePartBundle\Entity\ToTopPagePart'),
        );
    }

    function getPossiblePagePartTypes()
    {
        return $this->pagePartTypes;
    }

    function getName()
    {
        return "Pageparts";
    }

    function getDefaultContext()
    {
        return "main";
    }
}
