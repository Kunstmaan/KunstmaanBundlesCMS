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
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('content', 'textarea', array('label' => 'Content (raw html)', 'required' => false, 'attr' => array("style" => "width: 1000px", 'rows' => 20)));
    }

    /**
     * Returns the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'dashboardconfiguration';
    }

}
