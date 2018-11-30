<?php

namespace Kunstmaan\CacheBundle\Form\Varnish;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BanType.
 */
class BanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('path', TextType::class, [
            'label' => 'kunstmaan_cache.varnish.ban.path',
        ]);
        $builder->add('allDomains', CheckboxType::class, [
            'label' => 'kunstmaan_cache.varnish.ban.all_domains',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_cache_varnish_ban';
    }
}
