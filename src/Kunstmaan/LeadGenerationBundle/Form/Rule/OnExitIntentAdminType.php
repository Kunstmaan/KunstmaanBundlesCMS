<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\FormBuilderInterface;

class OnExitIntentAdminType extends AbstractRuleAdminType
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
        $builder->add('sensitivity', 'integer', array(
            'required' => false,
            'attr' => array(
                'info_text' => 'kuma_lead_generation.on_exit_intent.sensitivity_info'
            )
        ));
        $builder->add('timer', 'integer', array(
            'label' => 'Timer (milliseconds)',
            'required' => false,
            'attr' => array(
                'info_text' => 'kuma_lead_generation.on_exit_intent.timer_info'
            )
        ));
        $builder->add('delay', 'integer', array(
            'label' => 'Delay (milliseconds)',
            'required' => false,
            'attr' => array(
                'info_text' => 'kuma_lead_generation.on_exit_intent.delay_info'
            )
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'on_exit_intent_form';
    }
}
