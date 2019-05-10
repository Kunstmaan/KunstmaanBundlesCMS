<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class represents the type for the ChoiceFormSubmissionField
 */
class ChoiceFormSubmissionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $keys = array_fill_keys(['label', 'required', 'expanded', 'multiple', 'choices', 'placeholder', 'constraints'], null);
        $fieldOptions = array_filter(array_replace($keys, array_intersect_key($options, $keys)), function ($v) {
            return isset($v);
        });
        $fieldOptions['choices'] = array_flip($fieldOptions['choices']);
        $fieldOptions['empty_data'] = null;

        $builder->add('value', ChoiceType::class, $fieldOptions);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_choiceformsubmissiontype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField',
                'choices' => [],
                'placeholder' => null,
                'expanded' => null,
                'multiple' => null,
        ]);
    }
}
