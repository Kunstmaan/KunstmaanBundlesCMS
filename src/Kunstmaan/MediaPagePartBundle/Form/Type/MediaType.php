<?php

namespace Kunstmaan\MediaPagePartBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class MediaType extends AbstractType {
    protected $objectManager;

    public function __construct($objectManager) {
        $this->objectManager = $objectManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->prependClientTransformer(new IdToMediaTransformer($this->objectManager, $options['current_value_container']));
    }

    public function getParent()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
            'em'                => null,
            'class'             => "input_prop",
            'property'          => null,
            'query_builder'     => null,
            'choices'           => null,
            'chooserpath'		=> null,
            'current_value_container' => new CurrentValueContainer(),
        ));
    }

    public function getName() {
        return 'media';
    }
}
