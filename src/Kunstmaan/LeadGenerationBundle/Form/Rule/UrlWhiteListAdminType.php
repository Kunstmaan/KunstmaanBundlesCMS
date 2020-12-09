<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class UrlWhiteListAdminType extends AbstractRuleAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('urls', TextareaType::class, [
            'label' => 'kuma_lead_generation.form.url_white_list.urls.label',
            'attr' => [
                'info_text' => 'kuma_lead_generation.form.url_white_list.urls.info_text',
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'url_whitelist_form';
    }
}
