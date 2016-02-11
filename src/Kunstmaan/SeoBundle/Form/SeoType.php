<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SeoType
 */
class SeoType extends AbstractType
{
    const ROBOTS_NOINDEX = "noindex";
    const ROBOTS_NOFOLLOW = "nofollow";
    const ROBOTS_NOARCHIVE = "noarchive";
    const ROBOTS_NOSNIPPET = "nosnippet";
    const ROBOTS_NOTRANSLATE = "notranslate";
    const ROBOTS_NOIMAGEINDEX = "noimageindex";

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class)
        ->add('metaTitle', null, array(
                'label' => 'Title',
                'max_length' => 55,
                'attr' => array(
                'info_text' => 'The title tag is often used on search engine results pages. It should be less than 55 characters.'
            )
        ))
        ->add('metaDescription', null, array('label' => 'Meta description', 'max_length' => 155));

        $builder->add('metaRobots', ChoiceType::class, array(
          'choices' => array(
            'seo.form.robots.noindex'      => self::ROBOTS_NOINDEX,
            'seo.form.robots.nofollow'     => self::ROBOTS_NOFOLLOW,
            'seo.form.robots.noarchive'    => self::ROBOTS_NOARCHIVE,
            'seo.form.robots.nosnippet'    => self::ROBOTS_NOSNIPPET,
            'seo.form.robots.notranslate'  => self::ROBOTS_NOTRANSLATE,
            'seo.form.robots.noimageindex' => self::ROBOTS_NOIMAGEINDEX,
          ),
          'choices_as_values' => true,
          'max_length' => 255,
          'required' => false,
          'multiple' => true,
          'expanded' => false,
          'label' => 'Meta robots',
          'attr' => array(
            'class' => 'js-advanced-select form-control',
            'data-placeholder' => 'Choose robot tags',
          )));

        $builder->get('metaRobots')
            ->addModelTransformer(new CallbackTransformer(
                function ($original) {
                    // string to array
                    $array = explode(',', $original);
                    // trim all the values
                    $array = array_map('trim', $array);
                    return $array;
                },
                function ($submitted) {
                    // trim all the values
                    $value = array_map('trim', $submitted);
                    // join together
                    $string = implode(',', $value);
                    return $string;
                }
            ));
        $builder->add('extraMetadata', TextareaType::class, array('label' => 'Extra metadata', 'required' => false));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'seo';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        ));
    }
}
