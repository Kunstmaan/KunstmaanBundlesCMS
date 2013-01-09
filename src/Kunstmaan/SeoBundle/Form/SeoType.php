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
                ->add('metaAuthor')
                ->add('metaDescription')
                ->add('metaKeywords')
                ->add('metaRobots')
                ->add('metaRevised')
                ->add('ogType', null, array('label' => 'OG type'))
                ->add('ogTitle', null, array('label' => 'OG title'))
                ->add('ogDescription', null, array('label' => 'OG description'))
                ->add('ogImage', 'media', array(
                    'mediatype' => 'image',
                    'label' => 'OG image'
                ))
                ->add('extraMetadata', 'textarea')
                ->add('cimKeyword', 'text', array('required' => false, 'max_length' => 24));
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
