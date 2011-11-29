<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * class to define the form to upload a picture
 *
 */
class VideoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('content', 'text')
            ->add('videotype', 'choice', array(
                'choices'   => array('youtube' => 'youtube', 'vimeo' => 'vimeo', 'dailymotion' => 'dailymotion')))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_videotype';
    }
}

?>