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
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('rolesCollection', 'entity', array(
                        'class' => 'KunstmaanAdminBundle:Role',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('r')
                                ->orderBy('r.role', 'ASC');
                        },
                        'multiple' => true,
                        'expanded' => false,
                    )
                );
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
