<?php

namespace Kunstmaan\KAdminNodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * PagePartRefAdminType
 */
class PagePartRefAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('pageId');
        $builder->add('pageEntityname');
        $builder->add('context');
        $builder->add('sequencenumber');
        $builder->add('pagepartId');
        $builder->add('pagepartEntityname');
    }

    public function getName()
    {
        return 'page';
    }
}