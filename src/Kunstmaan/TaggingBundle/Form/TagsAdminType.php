<?php

namespace Kunstmaan\TaggingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Kunstmaan\TaggingBundle\Entity\TagManager;
use Kunstmaan\TaggingBundle\Form\DataTransformer\TagsTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getDefaultOptions(array $options)
    {
        $result = array();

        foreach ($this->tagManager->findAll() as $tag) {
            $result[$tag->getId()] = $tag->getName();
        }

        return array(
            'choices' => $result,
            'multiple' => TRUE,
            'required' => false,
            'attr' => array(
		'class' => 'js-advanced-select form-control advanced-select'
            )
        );
    }

    public function getParent()
    {
        return 'choice';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'kunstmaan_taggingbundle_tags';
    }
}
