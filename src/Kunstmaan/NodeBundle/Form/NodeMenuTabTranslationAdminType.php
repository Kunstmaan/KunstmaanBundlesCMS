<?php

namespace Kunstmaan\NodeBundle\Form;

use Kunstmaan\NodeBundle\Form\Type\SlugType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            $builder->add('slug', SlugType::class, array('required' => false));
        }
        $builder->add('weight', ChoiceType::class, array(
            'choices'     => array_combine(range(-50, 50), range(-50, 50)),
            'placeholder' => false,
            'required'    => false,
            'attr'        => array('title' => 'Used to reorder the pages.')
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
