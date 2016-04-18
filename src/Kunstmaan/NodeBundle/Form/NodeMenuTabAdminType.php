<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeMenuTabAdminType extends AbstractType
{
    /**
     * @var bool
     */
    private $isStructureNode;

    /**
     * @param bool $isStructureNode
     */
    public function __construct($isStructureNode = false)
    {
        $this->isStructureNode = $isStructureNode;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->isStructureNode) {
            $builder->add('hiddenFromNav', CheckboxType::class, array(
                'label' => 'kuma_node.form.menu_tab.hidden_from_menu.label', 
                'required' => false,
            ));
        }
        $builder->add('internalName', TextType::class, array(
            'label' => 'kuma_node.form.menu_tab.internal_name.label', 
            'required' => false,
        ));
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
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\NodeBundle\Entity\Node',
        ));
    }
}
