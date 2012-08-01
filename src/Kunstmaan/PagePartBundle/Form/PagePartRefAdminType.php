<?php

namespace Kunstmaan\KAdminNodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * PagePartRefAdminType
 */
class PagePartRefAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
