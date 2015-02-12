<?php

namespace Kunstmaan\PagePartBundle\Form;

use Kunstmaan\PagePartBundle\Entity\ButtonPagePart;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * ButtonPagePartAdminType
 */
class ButtonPagePartAdminType extends AbstractType
{
    /**
     * @var array
     */
    private $typeChoices;

    /**
     * @var array
     */
    private $sizeChoices;

    public function __construct()
    {
        $this->typeChoices = array();
        foreach (ButtonPagePart::$types as $type) {
            $this->typeChoices[$type] = 'pagepart.button.type.' . $type;
        }
        $this->sizeChoices = array();
        foreach (ButtonPagePart::$sizes as $size) {
            $this->sizeChoices[$size] = 'pagepart.button.size.' . $size;
        }
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(
                'linkUrl',
                'urlchooser',
                array(
                    'required' => true,
                )
            )
            ->add(
                'linkText',
                'text',
                array(
                    'required' => false,
                )
            )
            ->add(
                'linkNewWindow',
                'checkbox',
                array(
                    'required' => false,
                )
            )
            ->add(
                'type',
                'choice',
                array(
                    'choices'  => $this->typeChoices,
                    'required' => true,
                )
            )
            ->add(
                'size',
                'choice',
                array(
                    'choices'  => $this->sizeChoices,
                    'required' => true,
                )
            )
            ->add(
                'block',
                'checkbox',
                array(
                    'required' => false,
                )
            )
            ->add(
                'icon',
                'checkbox',
                array(
                    'required' => false,
                )
            )
            ->add(
                'center',
                'checkbox',
                array(
                    'required' => false,
                )
            );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_buttonpageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => '\Kunstmaan\PagePartBundle\Entity\ButtonPagePart'
            )
        );
    }
}