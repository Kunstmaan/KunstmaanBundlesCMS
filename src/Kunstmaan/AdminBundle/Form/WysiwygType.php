<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->mediaTokenTransformer);
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
