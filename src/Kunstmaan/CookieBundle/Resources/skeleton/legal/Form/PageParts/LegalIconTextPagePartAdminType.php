<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use {{ namespace }}\Entity\PageParts\LegalIconTextPagePart;


/**
 * LegalIconTextPagePartAdminType
 */
class LegalIconTextPagePartAdminType extends \Symfony\Component\Form\AbstractType
{
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

        $builder->add(
            'title',
            TextType::class,
            [
                'required' => true,
            ]
        );
        $builder->add(
            'subtitle',
            TextType::class,
            [
                'required' => true,
            ]
        );
        $builder->add(
            'content',
            WysiwygType::class,
            [
                'required' => true,
            ]
        );
        $builder->add(
            'icon',
            MediaType::class,
            [
                'required' => false,
            ]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'legal_icontextpageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LegalIconTextPagePart::class,
            ]
        );
    }
}
