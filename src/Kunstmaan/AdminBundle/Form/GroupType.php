<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * GroupType defines the form used for {@link Group}
 */
class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'settings.group.name',
                ]
            )
            ->add(
                'rolesCollection',
                EntityType::class,
                [
                    'label' => 'settings.group.roles',
                    'class' => 'KunstmaanAdminBundle:Role',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                            ->orderBy('r.role', 'ASC');
                    },
                    'multiple' => true,
                    'expanded' => false,
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'settings.group.roles_placeholder',
                        'class' => 'js-advanced-select form-control advanced-select',
                    ],
                ]
            );
    }

    public function getBlockPrefix()
    {
        return 'group';
    }
}
