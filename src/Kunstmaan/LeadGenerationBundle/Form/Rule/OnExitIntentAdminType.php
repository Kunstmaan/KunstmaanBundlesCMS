<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class OnExitIntentAdminType extends AbstractRuleAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sensitivity', IntegerType::class, [
            'label' => 'kuma_lead_generation.on_exit_intent.sensitivity.label',
            'required' => false,
            'attr' => [
                'info_text' => 'kuma_lead_generation.on_exit_intent.sensitivity_info',
            ],
        ]);
        $builder->add('timer', IntegerType::class, [
            'label' => 'kuma_lead_generation.on_exit_intent.timer.label',
            'required' => false,
            'attr' => [
                'info_text' => 'kuma_lead_generation.on_exit_intent.timer_info',
            ],
        ]);
        $builder->add('delay', IntegerType::class, [
            'label' => 'kuma_lead_generation.on_exit_intent.delay.label',
            'required' => false,
            'attr' => [
                'info_text' => 'kuma_lead_generation.on_exit_intent.delay_info',
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'on_exit_intent_form';
    }
}
