<?php

namespace Kunstmaan\TaggingBundle\Form;

use Kunstmaan\TaggingBundle\Entity\TagManager;
use Kunstmaan\TaggingBundle\Form\DataTransformer\TagsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsAdminType extends AbstractType
{
    protected $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TagsTransformer($this->tagManager);
        $builder->addViewTransformer($transformer, true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->getDefaultOptions(array()));
    }

    public function getDefaultOptions(array $options)
    {
        $result = array();

        foreach ($this->tagManager->findAll() as $tag) {
            $result[$tag->getName()] = $tag->getId();
        }

        return array(
          'choices' => $result,
          'multiple' => true,
          'required' => false,
          'attr' => array(
            'class' => 'js-advanced-select form-control advanced-select',
          ),
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_taggingbundle_tags';
    }
}
