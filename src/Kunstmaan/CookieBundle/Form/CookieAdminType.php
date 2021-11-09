<?php

namespace Kunstmaan\CookieBundle\Form;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CookieAdminType
 */
class CookieAdminType extends AbstractType
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => true,
                'label' => 'kuma.cookie.adminlists.cookie.name',
            ]
        );
        $builder->add(
            'type',
            EntityType::class,
            [
                'required' => true,
                'label' => 'kuma.cookie.adminlists.cookie.type',
                'class' => CookieType::class,
                'choice_label' => 'name',
            ]
        );
        $builder->add(
            'description',
            WysiwygType::class,
            [
                'required' => false,
                'label' => 'kuma.cookie.adminlists.cookie.description',
                'attr' => [
                    'height' => 100,
                ],
            ]
        );

        if ($options['domainConfiguration']->isMultiDomainHost()) {
            $hosts = $options['domainConfiguration']->getFullHostConfig();
            $domains = [];
            foreach ($hosts as $host) {
                $domains[] = $host['id'];
            }
            $domains = array_combine($domains, $domains);
            $domains = array_merge(['kuma.cookie.adminlists.filter.all' => ''], $domains);

            $builder->add('domain', ChoiceType::class, [
                'label' => 'kuma.cookie.form.domain',
                'choices' => $domains,
                'required' => true,
                'expanded' => false,
                'multiple' => false,
            ]);
        } else {
            $builder->add('domain', HiddenType::class, [
                'data' => $options['domainConfiguration']->getHost(),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'domainConfiguration' => null,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaancookiebundle_cookie_form';
    }
}
