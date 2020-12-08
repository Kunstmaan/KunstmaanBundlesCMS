<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Popup;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractPopupAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'kuma_lead_generation.form.popup.name.label',
            'attr' => [
                'info_text' => 'kuma_lead_generation.form.popup.name.info_text',
            ],
        ]);
        $builder->add('htmlId', TextType::class);
    }
}
