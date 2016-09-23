<?php

namespace Kunstmaan\NodeBundle\Form;

use Kunstmaan\NodeBundle\Form\Type\SlugType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class NodeMenuTabTranslationAdminType extends AbstractType
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
            $builder->add('slug', SlugType::class, array(
                'label' => 'kuma_node.form.menu_tab_translation.slug.label',
                'required' => false,
                'constraints' => array(
                    new Regex("/^[a-zA-Z0-9\-_\/]+$/")
                )
            ));
        }
        $builder->add('weight', ChoiceType::class, array(
            'label' => 'kuma_node.form.menu_tab_translation.weight.label',
            'choices'     => array_combine(range(-50, 50), range(-50, 50)),
            'placeholder' => false,
            'required'    => false,
            'attr'        => array('title' => 'kuma_node.form.menu_tab_translation.weight.title'),
            'choices_as_values' => true,
            'choice_translation_domain' => false
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'menutranslation';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\NodeBundle\Entity\NodeTranslation',
        ));
    }
}
