<?php

namespace Kunstmaan\CacheBundle\Form\Varnish;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BanType extends AbstractType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path', TextType::class, [
                'label' => 'kunstmaan_cache.varnish.ban.path',
            ])
            ->add('allDomains', CheckboxType::class, [
                'label' => 'kunstmaan_cache.varnish.ban.all_domains',
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_cache_varnish_ban';
    }
}
