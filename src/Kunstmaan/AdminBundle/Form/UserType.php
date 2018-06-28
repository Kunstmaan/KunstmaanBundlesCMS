<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Model\GroupInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * UserType defines the form used for {@link User}
 */
class UserType extends AbstractType implements RoleDependentUserFormInterface
{
    /**
     * @var bool
     */
    private $canEditAllFields = false;

    /**
     * Setter to check if we can display all form fields
     *
     * @param $canEditAllFields
     * @return bool
     */
    public function setCanEditAllFields($canEditAllFields)
    {
        $this->canEditAllFields = (bool)$canEditAllFields;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = array();
        foreach ($options['langs'] as $lang) {
            $languages[$lang] = $lang;
        }

        $this->canEditAllFields = $options['can_edit_all_fields'];

        $builder->add('username', TextType::class, array('required' => true, 'label' => 'settings.user.username'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => $options['password_required'],
                'invalid_message' => 'errors.password.dontmatch',
                'first_options' => array(
                    'label' => 'settings.user.password',
                ),
                'second_options' => array(
                    'label' => 'settings.user.repeatedpassword',
                ),
            ))
            ->add('email', EmailType::class, array('required' => true, 'label' => 'settings.user.email'))
            ->add('adminLocale', ChoiceType::class, array(
                'choices' => $languages,
                'label' => 'settings.user.adminlang',
                'required' => true,
                'placeholder' => false,
            ));

        if ($this->canEditAllFields) {

            $builder->add('enabled', CheckboxType::class,
                array('required' => false, 'label' => 'settings.user.enabled'));
            $groups = $builder->create('groups', EntityType::class, array(
                    'label' => 'settings.user.roles',
                    'class' => 'KunstmaanAdminBundle:Group',
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $this->getQueryBuilder($er, $options['can_add_super_users']);
                    },
                    'multiple' => true,
                    'expanded' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'settings.user.roles_placeholder',
                        'class' => 'js-advanced-select form-control advanced-select',
                    ),
                )
            );
            if (!$options['can_add_super_users']) {
                //When the user is not allowed to modify super users,
                // save any existing super user groups and add them manually to the user
                $existingSuperGroups = [];
                $groups->addEventListener(FormEvents::POST_SET_DATA,
                    function (\Symfony\Component\Form\FormEvent $event) use (&$existingSuperGroups) {
                        $groups = $event->getData();
                        if (!\is_iterable($groups)) {
                            return;
                        }
                        foreach ($groups as $group) {
                            if ($group instanceof GroupInterface && $group->hasRole('ROLE_SUPER_ADMIN')) {
                                \array_push($existingSuperGroups, $group);
                            }
                        }
                    });

                $groups->addEventListener(FormEvents::SUBMIT,
                    function (\Symfony\Component\Form\FormEvent $event) use (&$existingSuperGroups) {
                        $groups = $event->getData();
                        if ($groups instanceof Collection) {
                            foreach ($existingSuperGroups as $superGroup) {
                                $groups->add($superGroup);
                            }
                        }
                        $event->setData($groups);
                    });
            }
            $builder->add($groups);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'password_required' => false,
                'data_class' => 'Kunstmaan\AdminBundle\Entity\User',
                'langs' => null,
                'can_edit_all_fields' => false,
                'can_add_super_users' => false,
            )
        );
        $resolver->addAllowedValues('password_required', array(true, false));
    }

    private function getQueryBuilder(EntityRepository $repo, bool $canAddSuperUsers): QueryBuilder
    {
        $qb = $repo->createQueryBuilder('g');
        $qb->orderBy('g.name', 'ASC');
        if (!$canAddSuperUsers) {
            $superAdminGroupsBuilder = $repo->createQueryBuilder('_g');
            $superAdminGroupsBuilder->select('_g.id');
            $superAdminGroupsBuilder->join('_g.roles', '_r');
            $superAdminGroupsBuilder->where('_r.role = :role');

            $qb->where($qb->expr()->notIn('g.id', $superAdminGroupsBuilder->getDQL()));
            $qb->setParameter('role', 'ROLE_SUPER_ADMIN');
        }

        return $qb;
    }
}
