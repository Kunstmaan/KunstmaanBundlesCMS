<?php

namespace {{ namespace }}\PagePartAdmin;

use {{namespace}}\PagePartAdmin\ContentPagePartAdminConfigurator;

class FormPagePagePartAdminConfigurator extends ContentPagePartAdminConfigurator
{

    /**
     * @var array
     */
    private $pagePartTypes;

    /**
     * @param array $pagePartTypes
     */
    public function __construct(array $pagePartTypes = array())
    {
        parent::__construct($pagePartTypes);

        $this->pagePartTypes = array_merge(
            array(
                array(
                    'name' => 'Single Line text input',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart'
                ),
                array(
                    'name' => 'Multi Line text input',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart'
                ),
                array(
                    'name' => 'Choice input',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart'
                )
            ), $this->pagePartTypes
        );
    }

    /**
     * @return array
     */
    function getPossiblePagePartTypes()
    {
        return $this->pagePartTypes;
    }

    /**
     * @return string
     */
    function getName()
    {
        return "Page parts";
    }

    /**
     * @return string
     */
    function getDefaultContext()
    {
        return "main";
    }
}
