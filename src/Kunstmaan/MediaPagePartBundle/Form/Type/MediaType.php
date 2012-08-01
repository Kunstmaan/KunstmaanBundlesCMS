<?php

namespace Kunstmaan\MediaPagePartBundle\Form\Type;

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

    public function getDefaultOptions(array $options) {
        $defaultOptions = array(
            'em'                => null,
            'class'             => null,
            'property'          => null,
            'query_builder'     => null,
            'choices'           => null,
            'chooserpath'		=> null,
        );

        $options = array_replace($defaultOptions, $options);

        if (!isset($options['current_value_container'])) {
            $defaultOptions['current_value_container'] = new CurrentValueContainer();
        }

        return $defaultOptions;
    }

    public function getParent() {
        return 'field';
    }

    public function getName() {
        return 'media';
    }
}
