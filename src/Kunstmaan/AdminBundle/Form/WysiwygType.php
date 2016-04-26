<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
        return TextareaType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wysiwyg';
    }
}
