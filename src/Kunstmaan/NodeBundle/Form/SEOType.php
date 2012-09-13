<?php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class SEOType extends AbstractType
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
                    'pattern' => 'KunstmaanMediaBundle_chooser_imagechooser',
                    'label' => 'OG image'
                ))
                ->add('extraMetadata', 'textarea')
                ->add('cimKeyword', 'text', array('required' => true, 'max_length' => 24));
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
                'data_class' => 'Kunstmaan\AdminNodeBundle\Entity\Seo',
        );
    }
}
