<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class TocPagePartAdminType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
    }

    public function getName() {
        return 'kunstmaan_pagepartbundle_tocpageparttype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\PagePartBundle\Entity\TocPagePart',
        );
    }
}
