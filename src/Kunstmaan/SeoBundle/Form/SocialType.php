<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

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
            ->add('ogImage', 'media', array(
            'mediatype' => 'image',
            'label' => 'OG image'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'social';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        );
    }
}
