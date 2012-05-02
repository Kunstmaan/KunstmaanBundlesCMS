<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ToTopPagePartAdminType extends AbstractType {
    public function buildForm(FormBuilder $builder, array $options) {
    }

    public function getName() {
        return 'kunstmaan_pagepartbundle_totoppageparttype';
    }
}