<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

trigger_deprecation('kunstmaan/admin-bundle', '6.2', 'The "%s" class is deprecated and will be removed in 7.0. Use "%s" instead.', ColorType::class, \Symfony\Component\Form\Extension\Core\Type\ColorType::class);

/**
 * @deprecated since KunstmaanAdminBundle 6.2 and will be removed in KunstmaanAdminBundle 7.0. Use "Symfony\Component\Form\Extension\Core\Type\ColorType" instead.
 */
class ColorType extends AbstractType
{
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
