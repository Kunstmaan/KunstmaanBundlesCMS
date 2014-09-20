<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

{{ namespace }}\Entity\Satellite;

/**
 * The type for Satellite
 */
class SatelliteAdminType extends AbstractType
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
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('launched', 'date', array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy'
        ));
        $builder->add('link');
        $builder->add('weight', 'integer', array('label' => 'Launch mass (kg)'));
        $builder->add('type', 'choice', array(
            'choices' => array(
                Satellite::TYPE_COMMUNICATION => 'Communication satellite',
                Satellite::TYPE_CLIMATE => 'Climate research satellite',
                Satellite::TYPE_PASSIVE => 'Passive satellite'
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
        return 'satellite_form';
    }

}
