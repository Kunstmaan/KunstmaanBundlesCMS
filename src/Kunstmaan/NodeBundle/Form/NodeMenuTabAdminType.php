<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeMenuTabAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['available_in_nav']) {
            $builder->add('hiddenFromNav', CheckboxType::class, [
                'label' => 'kuma_node.form.menu_tab.hidden_from_menu.label',
                'required' => false,
            ]);
        }
        $builder->add('internalName', TextType::class, [
            'label' => 'kuma_node.form.menu_tab.internal_name.label',
            'required' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'menu';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\NodeBundle\Entity\Node',
            'available_in_nav' => true,
        ]);
    }
}
