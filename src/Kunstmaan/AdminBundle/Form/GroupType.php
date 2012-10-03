<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * GroupType
 */
class GroupType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('rolesCollection', 'entity', array(
            'class' => 'KunstmaanAdminBundle:Role',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('r')
                    ->orderBy('r.role', 'ASC');
            },
            'multiple' => true,
            'expanded' => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
