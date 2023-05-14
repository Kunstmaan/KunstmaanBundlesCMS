<?php

namespace Kunstmaan\RedirectBundle\Form;

use Kunstmaan\RedirectBundle\Form\EventSubscriber\RedirectPathNormalizerFormEventSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RedirectAdminType extends AbstractType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['domainConfiguration']->isMultiDomainHost()) {
            $hosts = $options['domainConfiguration']->getHosts();
            $domains = array_combine($hosts, $hosts);
            $domains = array_merge(['redirect.all' => ''], $domains);

            $builder->add('domain', ChoiceType::class, [
                'label' => 'redirect.form.redirect.domain.label',
                'choices' => $domains,
                'required' => true,
                'expanded' => false,
                'multiple' => false,
            ]);
        } else {
            $builder->add('domain', HiddenType::class, [
                'data' => '',
            ]);
        }

        $builder->add('origin', TextType::class, [
            'label' => 'redirect.form.redirect.origin.label',
            'required' => true,
            'attr' => [
                'info_text' => 'redirect.origin_info',
            ],
        ]);
        $builder->add('target', TextType::class, [
            'label' => 'redirect.form.redirect.target.label',
            'required' => true,
            'attr' => [
                'info_text' => 'redirect.target_info',
            ],
        ]);
        $builder->add('permanent', CheckboxType::class, [
            'label' => 'redirect.form.redirect.permanent.label',
            'required' => false,
        ]);
        $builder->add('note', TextType::class, [
            'label' => 'redirect.form.redirect.note.label',
            'required' => false,
        ]);

        $builder->get('origin')->addEventSubscriber(new RedirectPathNormalizerFormEventSubscriber());
        $builder->get('target')->addEventSubscriber(new RedirectPathNormalizerFormEventSubscriber());
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'domainConfiguration' => null,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'redirect_form';
    }
}
