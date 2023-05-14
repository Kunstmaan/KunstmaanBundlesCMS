<?php

namespace Kunstmaan\CookieBundle\Form;

use Kunstmaan\CookieBundle\Entity\CookieConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CookieAdminType
 */
class CookieConfigType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CookieConfig $config */
        $config = $builder->getData();

        $builder
            ->add(
                'clientIpAddresses',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'kuma.cookie.config.client_ip',
                ]
            )
            ->add(
                'internalIpAddresses',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'kuma.cookie.config.internal_ip',
                ]
            )
            ->add(
                'cookieVersion',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'kuma.cookie.config.version.label',
                    'attr' => [
                        'info_text' => 'kuma.cookie.config.version.info',
                        'min' => $config->getCookieVersion(),
                    ],
                ]
            )
            ->add(
                'cookieBundleEnabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'kuma.cookie.config.enabled',
                ]
            );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaancookiebundle_cookie_config';
    }
}
