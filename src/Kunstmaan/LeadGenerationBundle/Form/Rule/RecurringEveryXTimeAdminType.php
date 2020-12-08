<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RecurringEveryXTimeAdminType extends AbstractRuleAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.recurring_every_x_time.days.label',
            'required' => false,
        ]);
        $builder->add('hours', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.recurring_every_x_time.hours.label',
            'required' => false,
        ]);
        $builder->add('minutes', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.recurring_every_x_time.minutes.label',
            'required' => false,
        ]);
        $builder->add('times', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.recurring_every_x_time.times.label',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'recurring_every_x_time_form';
    }
}
