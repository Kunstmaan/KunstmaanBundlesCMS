<?php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class SEOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('metaauthor');
        $builder->add('metadescription');
        $builder->add('metakeywords');
        $builder->add('metarobots');
        $builder->add('metarevised');
        $builder->add('ogType', null, array('label' => 'OG type'));
        $builder->add('ogTitle', null, array('label' => 'OG title'));
        $builder->add('ogDescription', null, array('label' => 'OG description'));
        $builder->add('ogImage', 'media', array(
            'pattern' => 'KunstmaanMediaBundle_chooser_imagechooser',
            'label' => 'OG image'
        ));
        $builder->add('extraMetadata', 'textarea');
        $builder->add('cimKeyword', 'text', array('required' => true, 'max_length' => 24));
    }

    public function getName()
    {
        return 'seo';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\AdminNodeBundle\Entity\Seo',
        );
    }
}