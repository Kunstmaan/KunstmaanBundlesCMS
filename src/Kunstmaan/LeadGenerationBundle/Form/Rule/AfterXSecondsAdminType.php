<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class AfterXSecondsAdminType extends AbstractRuleAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('seconds', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.after_x_seconds.seconds.label',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'after_x_seconds_form';
    }
}
