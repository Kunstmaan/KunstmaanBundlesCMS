<?php

namespace Kunstmaan\TaggingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TagAdminType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'kuma_tagging.form.tag.name.label',
        ));
    }

    public function getBlockPrefix()
    {
        return 'tag_admin_form';
    }

}
