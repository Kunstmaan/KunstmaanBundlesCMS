<?php

namespace {{ namespace }}\PagePartAdmin;

class FormPagePagePartAdminConfigurator extends ContentPagePagePartAdminConfigurator
{

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

}
