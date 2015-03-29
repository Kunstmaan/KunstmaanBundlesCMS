<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * GroupType defines the form used for {@link Group}
 */
class GroupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                array(
                    'required' => true,
                    'label'    => 'settings.group.name'
                )
            )
            ->add(
                'rolesCollection',
                'entity',
                array(
                    'label'         => 'settings.group.roles',
                    'class'         => 'KunstmaanAdminBundle:Role',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                            ->orderBy('r.role', 'ASC');
                    },
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => true,
                    'attr'          => array(
			'class'            => 'js-advanced-select form-control advanced-select',
                        'data-placeholder' => 'Choose the roles...'
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'group';
    }
}
