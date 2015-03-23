<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This class represents the type for the TextFormWysiwygSubmissionField
 */
class WysiwygType extends AbstractType
{
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * @return string
     */
    public function getName()
    {
        // return 'kunstmaan_formbundle_Wysiwygformsubmissiontype';
        return 'wysiwyg';
    }
}
