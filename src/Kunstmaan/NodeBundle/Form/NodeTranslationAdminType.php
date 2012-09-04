<?php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class NodeTranslationAdminType extends AbstractType
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('node', 'entity_id', array('class' => 'Kunstmaan\AdminNodeBundle\Entity\Node'));
        $builder->add('slug');
        $builder->add('weight', 'choice', array(
                'choices'=>array_combine(range(-50,50), range(-50,50)),
                'empty_value' => false));
    }

    public function getName()
    {
        return 'nodetranslation';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\AdminNodeBundle\Entity\NodeTranslation',
        );
    }
}