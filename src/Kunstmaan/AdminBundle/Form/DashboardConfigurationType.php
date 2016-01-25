<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * DashboardConfigurationType
 */
class DashboardConfigurationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('content', TextareaType::class, array(
            'label' => 'Content (raw html)',
            'required' => false,
            'attr' => array(
                'style' => 'width: 1000px',
                'rows' => 20
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dashboardconfiguration';
    }

}
