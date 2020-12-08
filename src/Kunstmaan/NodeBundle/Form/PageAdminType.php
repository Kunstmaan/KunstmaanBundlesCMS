<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PageAdminType
 */
class PageAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);
        $builder->add('title', TextType::class, [
            'label' => 'kuma_node.form.page.title.label',
        ]);
        $builder->add('pageTitle', TextType::class, [
            'label' => 'kuma_node.form.page.page_title.label',
            'attr' => [
                'info_text' => 'kuma_node.form.page.page_title.info_text',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => 'Kunstmaan\NodeBundle\Entity\AbstractPage',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'page';
    }
}
