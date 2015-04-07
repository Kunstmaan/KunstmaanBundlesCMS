<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;


/**
 * Class WysiwygType
 * @package Kunstmaan\AdminBundle\Form
 */
class WysiwygType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wysiwyg';
    }
}
