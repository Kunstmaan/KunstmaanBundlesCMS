<?php

namespace Kunstmaan\FormBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * An abstract Form Page Admin Type
 */
class AbstractFormPageAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title');
        $builder->add('thanks', 'textarea', array('data_class' => 'Kunstmaan\BancontactBundle\Entity\FormPage', 'required' => false, 'attr' => array('class' => 'rich_editor')));
        $builder->add('subject');
        $builder->add('from_email');
        $builder->add('to_email');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'formpage';
    }
}
