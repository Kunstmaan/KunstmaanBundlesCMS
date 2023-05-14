<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated since KunstmaanAdminBundle 6.2 and will be removed in KunstmaanAdminBundle 7.0. Use "Symfony\Component\Form\Extension\Core\Type\ColorType" instead.
 */
class ColorType extends AbstractType
{
    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([TextType::class]);
    }

    /**
     * @return string|null
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
        return 'color';
    }
}
