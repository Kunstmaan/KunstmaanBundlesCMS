<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RecurringEveryXTimeAdminType extends AbstractRuleAdminType
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
        $builder->add('days', IntegerType::class, array(
            'required' => false
        ));
        $builder->add('hours', IntegerType::class, array(
            'required' => false
        ));
        $builder->add('minutes', IntegerType::class, array(
            'required' => false
        ));
        $builder->add('times', IntegerType::class);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'recurring_every_x_time_form';
    }
}
