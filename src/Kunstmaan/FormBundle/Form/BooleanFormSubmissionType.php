<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The type for the BooleanFormSubmissionField
 */
class BooleanFormSubmissionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $keys = array_fill_keys(array('label', 'required', 'constraints'), null);
        $fieldOptions = array_filter(array_replace($keys, array_intersect_key($options, $keys)), function ($v) {
            return isset($v);
        });

        $builder->add('value', CheckboxType::class, $fieldOptions);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_booleanformsubmissiontype';
    }
}
