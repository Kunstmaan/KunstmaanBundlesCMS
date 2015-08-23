<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

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
        $builder->add('days', 'integer', array(
            'required' => false
        ));
        $builder->add('hours', 'integer', array(
            'required' => false
        ));
        $builder->add('minutes', 'integer', array(
            'required' => false
        ));
        $builder->add('times', 'integer');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'recurring_every_x_time_form';
    }
}
