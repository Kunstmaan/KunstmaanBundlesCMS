<?php

namespace Kunstmaan\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
                ->add('adminLocale', 'choice', array(
                    'choices'     => $languages,
                    'label'       => 'settings.user.adminlang',
                    'required'    => true,
                    'empty_value' => false
                ));

        if ($this->canEditAllFields) {
            $builder->add('enabled', 'checkbox', array('required' => false, 'label' => 'settings.user.enabled'))
                    ->add('groups', 'entity', array(
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
    public function getName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
        array('password_required' => false,
            'data_class' => 'Kunstmaan\AdminBundle\Entity\User',
        ));
        $resolver->addAllowedValues(array('password_required' => array(true, false)));
    }
}
