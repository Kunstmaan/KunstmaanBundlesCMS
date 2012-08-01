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
}
