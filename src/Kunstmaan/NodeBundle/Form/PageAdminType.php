<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * PageAdminType
 */
class PageAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('title', null, array('label' => 'Name'));
        $builder->add('pageTitle');
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\NodeBundle\Entity\AbstractPage',
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'page';
    }
}
