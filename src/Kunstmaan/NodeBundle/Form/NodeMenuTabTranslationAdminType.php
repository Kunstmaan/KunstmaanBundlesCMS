<?php

namespace Kunstmaan\NodeBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Form\Type\SlugType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class NodeMenuTabTranslationAdminType extends AbstractType
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['slugable']) {
            $builder->add('slug', SlugType::class, [
                'label' => 'kuma_node.form.menu_tab_translation.slug.label',
                'required' => false,
                'constraints' => [
                    new Regex("/^[a-zA-Z0-9\-_\/]+$/"),
                ],
            ]);
        }
        $builder->add('weight', ChoiceType::class, [
            'label' => 'kuma_node.form.menu_tab_translation.weight.label',
            'choices' => array_combine(range(-50, 50), range(-50, 50)),
            'placeholder' => false,
            'required' => false,
            'attr' => ['title' => 'kuma_node.form.menu_tab_translation.weight.title'],
            'choice_translation_domain' => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $nt = $event->getData();
            if ($nt instanceof NodeTranslation && $nt->getNode()->getParent() !== null) {
                $maxWeight = $this->em->getRepository(NodeTranslation::class)->getMaxChildrenWeight($nt->getNode()->getParent());
                $minWeight = $this->em->getRepository(NodeTranslation::class)->getMinChildrenWeight($nt->getNode()->getParent());
                $options = $event->getForm()->get('weight')->getConfig()->getOptions();
                $options['choices'] = array_combine(range($minWeight - 1, $maxWeight + 1), range($minWeight - 1, $maxWeight + 1));

                $event->getForm()->add('weight', ChoiceType::class, $options);
            }
        });
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'menutranslation';
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\NodeBundle\Entity\NodeTranslation',
            'slugable' => true,
        ]);
    }
}
