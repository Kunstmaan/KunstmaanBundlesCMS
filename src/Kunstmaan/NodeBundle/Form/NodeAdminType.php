<?php
// src/Blogger/BlogBundle/Form/EnquiryType.php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NodeAdminType extends AbstractType
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
       	
    }

    public function getName()
    {
        return 'node';
    }
}