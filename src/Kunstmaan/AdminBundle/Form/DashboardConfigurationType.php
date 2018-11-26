<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder->add('title', TextType::class, array(
            'label' => 'kuma_admin.dashboard.configuration.title.label',
        ));
        $builder->add('content', TextareaType::class, array(
            'label' => 'kuma_admin.dashboard.configuration.content.label',
            'required' => false,
            'attr' => array(
                'style' => 'width: 1000px',
                'rows' => 20,
            ),
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
