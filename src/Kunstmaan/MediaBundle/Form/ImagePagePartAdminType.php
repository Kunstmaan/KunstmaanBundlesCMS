<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class ImagePagePartAdminType extends AbstractType {
    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('title', null, array('required' => false));
        $builder->add('link', 'url', array('required' => false));
        $builder->add('alttext', null, array('required' => false));

        $builder->add('media', 'entity', array(
            'required'  => false,
            'class'     => 'KunstmaanMediaBundle:Media',
            'property'  => 'url',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('b')->where('b.classtype = ?1')->setParameter(1, 'Image');
            },
            'attr'    => array(
                'class' => 'imagechooser'
            )
        ));
    }

    public function getName() {
        return 'kunstmaan_mediabundle_imagepageparttype';
    }
}