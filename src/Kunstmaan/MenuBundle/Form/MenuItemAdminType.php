<?php

namespace Kunstmaan\MenuBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemAdminType extends AbstractType
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
        $entityId = $options['entityId'];
        $menu     = $options['menu'];
        $menuItemclass = $options['menuItemClass'];

        $builder->add(
            'parent',
            EntityType::class,
            array(
                'class'         => $menuItemclass,
                'choice_label'  => 'displayTitle',
                'query_builder' => function (EntityRepository $er) use (
                    $entityId,
                    $menu
                ) {
                    $qb = $er->createQueryBuilder('mi')
                        ->where('mi.menu = :menu')
                        ->setParameter('menu', $menu)
                        ->orderBy('mi.lft', 'ASC');
                    if ($entityId) {
                        $qb->andWhere('mi.id != :id')
                            ->setParameter('id', $entityId);
                    }

                    return $qb;
                },
                'attr'          => array(
                    'class'       => 'js-advanced-select',
                    'placeholder' => 'kuma_menu.form.parent_placeholder'
                ),
                'multiple'      => false,
                'expanded'      => false,
                'required'      => false,
                'label'         => 'kuma_menu.form.parent'
            )
        );
        $builder->add(
            'type',
            ChoiceType::class,
            array(
                'choices'     => array_combine(
                    MenuItem::$types,
                    MenuItem::$types
                ),
                'placeholder' => false,
                'required'    => true,
                'choices_as_values' => true,
                'label' => 'kuma_menu.form.type'
            )
        );
        $locale   = $options['locale'];
        $rootNode = $options['rootNode'];

        $builder->add(
            'nodeTranslation',
            EntityType::class,
            array(
                'class'         => 'KunstmaanNodeBundle:NodeTranslation',
                'choice_label'  => 'title',
                'query_builder' => function (EntityRepository $er) use (
                    $locale,
                    $rootNode
                ) {
                    $qb = $er->createQueryBuilder('nt')
                        ->innerJoin('nt.publicNodeVersion', 'nv')
                        ->innerJoin('nt.node', 'n')
                        ->where('n.deleted = 0')
                        ->andWhere('nt.lang = :lang')
                        ->setParameter('lang', $locale)
                        ->andWhere('nt.online = 1')
                        ->orderBy('nt.title', 'ASC');
                    if ($rootNode) {
                        $qb->andWhere('n.lft >= :left')
                            ->andWhere('n.rgt <= :right')
                            ->setParameter('left', $rootNode->getLeft())
                            ->setParameter('right', $rootNode->getRight());
                    }

                    return $qb;
                },
                'attr'          => array(
                    'class'       => 'js-advanced-select',
                    'placeholder' => 'kuma_menu.form.node_translation_placeholder'
                ),
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'label'         => 'kuma_menu.form.node_translation'
            )
        );
        $builder->add(
            'title',
            TextType::class,
            array(
                'required' => false,
                'label' => 'kuma_menu.form.title'
            )
        );
        $builder->add(
            'url',
            TextType::class,
            array(
                'required' => true,
                'label' => 'kuma_menu.form.url'
            )
        );
        $builder->add(
            'newWindow',
            CheckboxType::class,
            array(
                'label' => 'kuma_menu.form.new_window'
            )
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
          array(
              'data_class' => MenuItem::class,
              'menu' => null,
              'entityId' => null,
              'rootNode' => null,
              'menuItemClass' => null,
              'locale' => null,
          )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'menuitem_form';
    }
}
