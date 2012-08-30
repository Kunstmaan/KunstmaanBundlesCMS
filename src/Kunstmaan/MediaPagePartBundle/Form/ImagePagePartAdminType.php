<?php

namespace Kunstmaan\MediaPagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class ImagePagePartAdminType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('link', 'url', array('required' => false));
        $builder->add('openinnewwindow', 'checkbox', array('required' => false));
        $builder->add('alttext', null, array('required' => false));
        $builder->add('media', 'media', array('pattern' => 'KunstmaanMediaBundle_chooser_imagechooser'));
    }

    public function getName() {
        return 'kunstmaan_mediabundle_imagepageparttype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart',
        );
    }
}
