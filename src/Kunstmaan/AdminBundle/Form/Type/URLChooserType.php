<?php

namespace Kunstmaan\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * URLChooserType
 */
class URLChooserType extends AbstractType
{
    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return $options;
    }

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
