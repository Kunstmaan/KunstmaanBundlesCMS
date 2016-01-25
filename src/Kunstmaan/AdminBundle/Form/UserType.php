<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @var array
     */
    private $langs = array();

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
     * @param array $langs
     */
    public function setLangs(array $langs)
    {
        $this->langs = $langs;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = array();
        foreach ($this->langs as $lang) {
            $languages[$lang] = $lang;
        }

        $builder->add('username', TextType::class, array ('required' => true, 'label' => 'settings.user.username'))
                ->add('plainPassword', RepeatedType::class, array(
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
                ->add('email', EmailType::class, array ('required' => true, 'label' => 'settings.user.email'))
                ->add('adminLocale', 'choice', array(
                    'choices'     => $languages,
                    'label'       => 'settings.user.adminlang',
                    'required'    => true,
                    'placeholder' => false
                ));

        if ($this->canEditAllFields) {
            $builder->add('enabled', CheckboxType::class, array('required' => false, 'label' => 'settings.user.enabled'))
                    ->add('groups', EntityType::class, array(
                            'label' => 'settings.user.roles',
                            'class' => 'KunstmaanAdminBundle:Group',
                            'query_builder' => function(EntityRepository $er) {
                                return $er->createQueryBuilder('g')
                                    ->orderBy('g.name', 'ASC');
                            },
                            'multiple' => true,
                            'expanded' => false,
                            'required' => false,
                            'attr' => array('class' => 'js-advanced-select form-control advanced-select',
                                'data-placeholder' => 'Choose the permission groups...'
                            )
                        )
                    );
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
        array('password_required' => false,
            'data_class' => 'Kunstmaan\AdminBundle\Entity\User',
        ));
        $resolver->addAllowedValues('password_required', array(true, false));
    }
}
