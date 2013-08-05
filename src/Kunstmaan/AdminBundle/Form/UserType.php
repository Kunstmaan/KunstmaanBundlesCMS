<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * UserType defines the form used for {@link User}
 */
class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array ('required' => true, 'label' => 'settings.user.username'))
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'required' => $options['password_required'],
                    'invalid_message' => "errors.password.dontmatch",
                    'first_options' => array(
                        'label' => 'settings.user.password'
                    ),
                    'second_options' => array(
                        'label' => 'settings.user.repeatedpassword'
                    )
                    )
                )
                ->add('email', 'email', array ('required' => true, 'label' => 'settings.user.email'))
                ->add('enabled', 'checkbox', array('required' => false, 'label' => 'settings.user.enabled'))
                ->add('groups', 'entity', array(
                        'label' => 'settings.user.roles',
                        'class' => 'KunstmaanAdminBundle:Group',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('g')
                                ->orderBy('g.name', 'ASC');
                        },
                        'multiple' => true,
                        'expanded' => false,
                        'required' => false
                    )
                );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('password_required' => false));
        $resolver->addAllowedValues(array('password_required' => array(true, false)));
    }

}
