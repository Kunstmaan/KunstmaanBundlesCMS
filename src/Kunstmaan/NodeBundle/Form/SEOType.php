<?php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SEOType extends AbstractType
{
	public function __construct(){
	}
	
    public function buildForm(FormBuilder $builder, array $options)
    {	
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
    }

    public function getName()
    {
        return 'seo';
    }
}