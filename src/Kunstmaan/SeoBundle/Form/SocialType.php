<?php

namespace Kunstmaan\SeoBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        // OPEN GRAPH
        $builder->add('id', 'hidden')
            ->add('ogTitle', 'text', array(
                'label'     => 'seo.form.og.title',
                'required'  => false,
                'attr'      => array(
                    'info_text'     => "Open Graph (OG) is a standard way of representing online objects. It's used, as example, by Facebook or other social media to build share links."
                )
            ))
            ->add('ogDescription', 'textarea', array(
                'label' => 'seo.form.og.description',
                'required'  => false,
            ))
            ->add('ogUrl', 'urlchooser', array(
                'label' => 'seo.form.og.url',
                'required'  => false,
            ))
            ->add('ogType', 'choice', array(
                'label'     => 'seo.form.og.type',
                'required'  => false,
                'choices'   => array(
                    "website"       => "Website",
                    "article"       => "Article",
                    "profile"       => "Profile",
                    "book"          => "Book",
                    "video.other"   => "Video",
                    "music.song"    => "Music"
                )
            ))
            ->add('ogImage', 'media', array(
                'label' => 'seo.form.og.image',
                'required'  => false
            ));
        $builder
            ->add('ogArticleAuthor', 'text',
                array(
                    'label' => 'OG Article Author',
                    'required' => false
                ))
            ->add('ogArticlePublisher', 'text',
                array(
                    'label' => 'OG Article Publisher',
                    'required' => false
                ))
            ->add('ogArticleSection', 'text',
                array(
                    'label' => 'OG Article Section',
                    'required' => false
                ));



        // TWITTER
        $builder->add('twitterTitle', 'text', array(
            'label' => 'seo.form.twitter.title',
            'required'  => false,
            'attr'      => array(
                'info_text'     => "The title of your twitter card. Falls back to SEO Meta title"
            )
        ))
            ->add('twitterDescription', 'textarea', array(
                'label' => 'seo.form.twitter.description',
                'required'  => false,
                'attr'      => array(
                    'info_text'     => "The description of your twitter card. Falls back to SEO Meta description"
                )
            ))
            ->add('twitterSite', 'text', array(
                'label' => 'seo.form.twitter.sitehandle',
                'required'  => false,
                'attr'      => array(
                    'info_text'     => "Twitter handle of your website organisation. This value is required for twitter cards to work."
                )
            ))
            ->add('twitterCreator', 'text', array(
                'label' => 'seo.form.twitter.creatorhandle',
                'required'  => false,
                'attr'      => array(
                    'info_text'     => "Twitter handle of your page publisher."
                )
            ))
            ->add('twitterImage', 'media', array(
                'label' => 'seo.form.twitter.image',
                'required' => false,
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'social';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\SeoBundle\Entity\Seo',
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }
}
