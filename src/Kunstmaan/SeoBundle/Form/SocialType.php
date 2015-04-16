<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SocialType. Uses the same SEO entity as the SeoType in order to prevent dataloss because there
 * is no easy way to expose a Migration from the bundles.
 */
class SocialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden')
            ->add('ogType', null, array('label' => 'OG type'))
            ->add('ogTitle', null, array('label' => 'OG title'))
            ->add('ogDescription', null, array('label' => 'OG description'))
            ->add('ogImage', 'media', array('mediatype' => 'image', 'label' => 'OG image', 'required' => false))
            ->add('ogUrl', null, array('label' => 'OG Url'))
            ->add('linkedInRecommendLink', "text", array("required" => false, 'label' => 'LinkedIn Recommend Link'))
            ->add('linkedInRecommendProductID', "text", array("required" => false, 'label' => 'LinkedIn Product ID'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'social';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        ));
    }
}
