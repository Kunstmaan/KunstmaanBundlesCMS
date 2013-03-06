<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

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
                ->add('metaAuthor', null, array('label' => 'Meta author'))
                ->add('metaDescription', null, array('label' => 'Meta description'))
                ->add('metaKeywords', null, array('label' => 'Meta keywords'))
                ->add('metaRobots', null, array('label' => 'Meta robots'))
                ->add('metaRevised', null, array('label' => 'Meta revised'))
                ->add('extraMetadata', 'textarea', array('label' => 'Extra metadata', 'required' => false))
                ->add('cimKeyword', 'text', array('label' => 'Cim keyword', 'required' => false, 'max_length' => 24));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'seo';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        );
    }
}
