<?php

namespace Kunstmaan\MediaPagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class VideoPagePartAdminType extends AbstractType {
    public function buildForm(FormBuilder $builder, array $options) {
    	$builder->add('media', 'media', array('pattern' => 'KunstmaanMediaBundle_chooser_videochooser'));
    	/*$builder->add('media', 'entity', array(
            'required'  => false,
            'class'     => 'KunstmaanMediaBundle:Media',
            'property'  => 'url',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('b')->where('b.classtype = ?1')->setParameter(1, 'Video');
            },
            //'attr' => array('class' => 'hidden')
        ));*/
    }

    public function getName() {
        return 'kunstmaan_mediabundle_videopageparttype';
    }
}