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
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'kuma_lead_generation.form.popup.name.label',
            'attr' => array(
                'info_text' => 'kuma_lead_generation.form.popup.name.info_text',
            ),
        ));
        $builder->add('htmlId', TextType::class);
    }
}
