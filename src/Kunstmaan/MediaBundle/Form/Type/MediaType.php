<?php

namespace Kunstmaan\MediaBundle\Form\Type;

use Doctrine\Persistence\ObjectManager;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * MediaType
 */
class MediaType extends AbstractType
{
    /**
     * @var MediaManager
     */
    protected $mediaManager;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param MediaManager  $mediaManager  The media manager
     * @param ObjectManager $objectManager The media manager
     */
    public function __construct($mediaManager, $objectManager)
    {
        $this->mediaManager = $mediaManager;
        $this->objectManager = $objectManager;
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
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(
            new IdToMediaTransformer($this->objectManager, $options['current_value_container']),
            true
        );
        $builder->setAttribute('chooser', $options['chooser']);
        $builder->setAttribute('mediatype', $options['mediatype']);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return FormType::class;
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'compound' => false,
                'chooser' => 'KunstmaanMediaBundle_chooser',
                'mediatype' => null,
                'current_value_container' => new CurrentValueContainer(),
                // @experimental The option below are for a feature is experimental and is a subject to change, be advised when using this feature and classes.
                'show_image_edit_modal' => false,
                'use_focus_point' => false,
                'use_cropping' => false,
                'focus_point_classes' => '',
                'cropping_views' => '',
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'media';
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['chooser'] = $form->getConfig()->getAttribute('chooser');
        $view->vars['mediatype'] = $form->getConfig()->getAttribute('mediatype');
        $view->vars['mediamanager'] = $this->mediaManager;
        $view->vars['show_image_edit_modal'] = $options['show_image_edit_modal'];
        $view->vars['use_focus_point'] = $options['use_focus_point'];
        $view->vars['use_cropping'] = $options['use_cropping'];
        $view->vars['focus_point_classes'] = $options['focus_point_classes'];
        $view->vars['cropping_views'] = $options['cropping_views'];
    }
}
