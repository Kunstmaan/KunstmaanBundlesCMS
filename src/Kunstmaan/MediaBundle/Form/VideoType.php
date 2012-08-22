<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('content', 'text')
            ->add('type', 'choice', array(
                'choices'   => array('youtube' => 'youtube', 'vimeo' => 'vimeo', 'dailymotion' => 'dailymotion')))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_videotype';
    }
}