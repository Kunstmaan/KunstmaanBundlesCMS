<?php

namespace Kunstmaan\MediaPagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class ImagePagePartAdminType extends AbstractType {
    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('link', 'url', array('required' => false));
        $builder->add('openinnewwindow', 'checkbox', array('required' => false));
        $builder->add('alttext', null, array('required' => false));
        $builder->add('media', 'media');
    }

    public function getName() {
        return 'kunstmaan_mediabundle_imagepageparttype';
    }
}