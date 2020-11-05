<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class SeoType extends AbstractType
{
    const ROBOTS_NOINDEX = 'noindex';
    const ROBOTS_NOFOLLOW = 'nofollow';
    const ROBOTS_NOARCHIVE = 'noarchive';
    const ROBOTS_NOSNIPPET = 'nosnippet';
    const ROBOTS_NOTRANSLATE = 'notranslate';
    const ROBOTS_NOIMAGEINDEX = 'noimageindex';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class)
            ->add('metaTitle', TextType::class, [
                'required' => false,
                'label' => 'seo.form.seo.meta_title.label',
                'attr' => [
                    'info_text' => 'seo.form.seo.meta_title.info_text',
                    'maxlength' => 70,
                ],
                'constraints' => [
                    new Length([
                        'max' => 70,
                    ]),
                ],
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => false,
                'label' => 'seo.form.seo.meta_description.label',
                'attr' => [
                    'maxlength' => 300,
                ],
                'constraints' => [
                    new Length([
                        'max' => 300,
                    ]),
                ],
            ]);

        $builder->add('metaRobots', ChoiceType::class, [
            'choices' => [
                'seo.form.robots.noindex' => self::ROBOTS_NOINDEX,
                'seo.form.robots.nofollow' => self::ROBOTS_NOFOLLOW,
                'seo.form.robots.noarchive' => self::ROBOTS_NOARCHIVE,
                'seo.form.robots.nosnippet' => self::ROBOTS_NOSNIPPET,
                'seo.form.robots.notranslate' => self::ROBOTS_NOTRANSLATE,
                'seo.form.robots.noimageindex' => self::ROBOTS_NOIMAGEINDEX,
            ],
            'required' => false,
            'multiple' => true,
            'expanded' => false,
            'label' => 'seo.form.seo.meta_robots.label',
            'attr' => [
                'placeholder' => 'seo.form.seo.meta_robots.placeholder',
                'class' => 'js-advanced-select form-control',
                'maxlength' => 255,
            ],
        ]);

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
                    return implode(',', $value);
                }
            ));
        $builder->add('extraMetadata', TextareaType::class, [
            'label' => 'seo.form.seo.extra_metadata.label',
            'required' => false,
        ]);
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
        $resolver->setDefaults([
                'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        ]);
    }
}
