<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class AfterXScrollPercentRuleAdminType extends AbstractRuleAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('percentage', IntegerType::class, [
            'label' => 'kuma_lead_generation.form.after_x_scroll_percent_rule.percentage.label',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'after_x_percent_form';
    }
}
