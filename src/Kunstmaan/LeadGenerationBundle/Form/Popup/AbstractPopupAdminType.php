<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Popup;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractPopupAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'attr' => array('info_text' => 'Warning: if you change this value, the people who already saw this popup, or the people who were already converted will see the popup again.')
        ));
        $builder->add('htmlId', TextType::class);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    abstract function getBlockPrefix();
}
