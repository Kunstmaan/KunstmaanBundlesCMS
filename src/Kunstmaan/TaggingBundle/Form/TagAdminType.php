<?php

namespace Kunstmaan\TaggingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TagAdminType extends AbstractType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'kuma_tagging.form.tag.name.label',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'tag_admin_form';
    }
}
