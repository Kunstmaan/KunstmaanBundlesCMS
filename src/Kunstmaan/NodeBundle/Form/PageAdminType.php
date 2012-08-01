<?php
// src/Blogger/BlogBundle/Form/EnquiryType.php

namespace Kunstmaan\AdminNodeBundle\Form;
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
        $builder->add('title');
        $builder->add('pageTitle');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'page';
    }
}
