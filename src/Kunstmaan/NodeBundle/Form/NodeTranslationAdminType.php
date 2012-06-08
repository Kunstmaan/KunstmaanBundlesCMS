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
        $builder->add('weight', 'choice', array(
                'choices'=>array_combine(range(-50,50), range(-50,50)),
                'empty_value' => false));
    }

    public function getName()
    {
        return 'nodetranslation';
    }
}