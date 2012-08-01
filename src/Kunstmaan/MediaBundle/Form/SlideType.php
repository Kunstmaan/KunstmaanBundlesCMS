<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class SlideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('content', 'text')
            ->add('type', 'choice', array(
                'choices'   => array('speakerdeck' => 'speakerdeck', 'slideshare' => 'slideshare')))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_slidetype';
    }
}