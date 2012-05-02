<?php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NodeTranslationAdminType extends AbstractType
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('online', 'checkbox', array('required' => false));
        $builder->add('slug');
    }

    public function getName()
    {
        return 'nodetranslation';
    }
}