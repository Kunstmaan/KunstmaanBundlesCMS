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
                    'name' => 'Email input',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart'
                ),
                array(
                    'name' => 'File upload',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart'
                ),
                array(
                    'name' => 'Choice input',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart'
                ),
                array(
                    'name' => 'Submit button',
                    'class'=> 'Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart'
                )
            ), $this->pagePartTypes
        );
    }

}
