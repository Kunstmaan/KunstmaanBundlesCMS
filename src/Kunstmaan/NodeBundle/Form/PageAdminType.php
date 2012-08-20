<?php
// src/Blogger/BlogBundle/Form/EnquiryType.php

namespace Kunstmaan\AdminNodeBundle\Form;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * PageAdminType
 */
class PageAdminType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('title');
        $builder->add('pageTitle');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\AdminNodeBundle\Entity\AbstractPage',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'page';
    }
}
