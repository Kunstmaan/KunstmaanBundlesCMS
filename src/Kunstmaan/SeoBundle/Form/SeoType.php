<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SeoType
 */
class SeoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden')
		->add('metaTitle', null, array(
		    'label' => 'Meta title',
		    'attr' => array(
			'info_text' => 'Sets the title in the head of your document. It should be short and descriptive. The content of the "Navigation title" field will be used if this field is left blank.'
		    )
		))
                ->add('metaAuthor', null, array('label' => 'Meta author'))
                ->add('metaDescription', null, array('label' => 'Meta description'))
                ->add('metaKeywords', null, array('label' => 'Meta keywords'))
                ->add('metaRobots', null, array('label' => 'Meta robots'))
                ->add('metaRevised', null, array('label' => 'Meta revised'))
                ->add('extraMetadata', 'textarea', array('label' => 'Extra metadata', 'required' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'seo';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        ));
    }
}
