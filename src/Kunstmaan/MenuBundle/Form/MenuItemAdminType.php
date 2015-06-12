<?php

namespace Kunstmaan\MenuBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\MenuBundle\Entity\Menu;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class MenuItemAdminType extends AbstractType
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var Menu
     */
    private $menu;

    /**
     * @var int
     */
    private $entityId;

    /**
     * @param string $locale
     * @param Menu $menu
     * @param int|null $entityId
     */
    public function __construct($locale, $menu, $entityId = null)
    {
        $this->locale = $locale;
        $this->menu = $menu;
        $this->entityId = $entityId;
    }

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
        $entityId = $this->entityId;
        $menu = $this->menu;
        $builder->add('parent', 'entity', array(
            'class' => 'KunstmaanMenuBundle:MenuItem',
            'property' => 'selectTitle',
            'query_builder' => function(EntityRepository $er) use ($entityId, $menu) {
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
            'attr' => array(
                'class' => 'js-advanced-select',
                'data-placeholder' => 'Select the parent menu item...'
            ),
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'label' => 'Parent menu item'
        ));
        $builder->add('type', 'choice', array(
            'choices' => array_combine(MenuItem::$types, MenuItem::$types),
            'empty_value' => false,
            'required' => true
        ));
        $locale = $this->locale;
        $builder->add('nodeTranslation', 'entity', array(
            'class' => 'KunstmaanNodeBundle:NodeTranslation',
            'property' => 'title',
            'query_builder' => function(EntityRepository $er) use ($locale) {
                return $er->createQueryBuilder('nt')
                    ->innerJoin('nt.publicNodeVersion', 'nv')
                    ->innerJoin('nt.node', 'n')
                    ->where('n.deleted = 0')
                    ->andWhere('nt.lang = :lang')
                    ->setParameter('lang', $locale)
                    ->andWhere('nt.online = 1')
                    ->orderBy('nt.title', 'ASC');
            },
            'attr' => array(
                'class' => 'js-advanced-select',
                'data-placeholder' => 'Select the page to link to...'
            ),
            'multiple' => false,
            'expanded' => false,
            'required' => true,
            'label' => 'Link page'
        ));
        $builder->add('title', 'text', array(
            'required' => false
        ));
        $builder->add('url', 'text', array(
            'required' => true
        ));
        $builder->add('newWindow');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'menuitem_form';
    }
}
