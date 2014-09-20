<?php

namespace {{ namespace }}\Form\Pages;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

{{ namespace }}\Entity\Satellite;

/**
 * SatelliteOverviewPageAdminType
 */
class SatelliteOverviewPageAdminType extends \Kunstmaan\NodeBundle\Form\PageAdminType
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add(
            'type',
            'choice',
            array(
                'choices' => array(
                    Satellite::TYPE_COMMUNICATION => 'Communication satellite',
                    Satellite::TYPE_CLIMATE => 'Climate research satellite',
                    Satellite::TYPE_PASSIVE => 'Passive satellite'
                ),
                'required' => false,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'satelliteoverviewpagetype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\{{ namespace }}\Entity\Pages\SatelliteOverviewPage'
        ));
    }
}
