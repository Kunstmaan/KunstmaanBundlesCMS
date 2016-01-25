<?php

namespace Kunstmaan\NodeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * URLChooserType
 */
class URLChooserType extends AbstractType
{

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'urlchooser';
    }
}
