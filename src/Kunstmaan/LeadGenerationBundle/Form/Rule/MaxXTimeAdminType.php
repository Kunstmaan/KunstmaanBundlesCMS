<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class MaxXTimeAdminType extends AbstractRuleAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('times', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.max_x_time.times.label',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'max_x_times_form';
    }
}
