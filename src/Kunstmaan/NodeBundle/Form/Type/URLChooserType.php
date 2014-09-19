<?php

namespace Kunstmaan\NodeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

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
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'urlchooser';
    }
}
