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
        $builder->add('extraMetadata', 'textarea');
    }

    public function getName()
    {
        return 'seo';
    }
}