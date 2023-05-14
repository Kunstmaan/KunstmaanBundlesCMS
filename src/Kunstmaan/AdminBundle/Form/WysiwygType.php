<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WysiwygType extends AbstractType
{
    /**
     * @var DataTransformerInterface
     */
    private $mediaTokenTransformer;

    public function __construct(DataTransformerInterface $mediaTokenTransformer)
    {
        $this->mediaTokenTransformer = $mediaTokenTransformer;
    }

    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->mediaTokenTransformer);
    }

    /**
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['editor-mode'])) {
            $view->vars['attr']['data-editor-mode'] = $options['editor-mode'];
        }
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('editor-mode');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wysiwyg';
    }
}
