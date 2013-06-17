<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * UserType defines the form used for {@link User}
 */
class DashboardConfigurationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('content', 'textarea', array('label' => 'Content (raw html)', 'required' => false, 'attr' => array("style" => "width: 1000px", 'rows' => 20)));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dashboardconfiguration';
    }

}
