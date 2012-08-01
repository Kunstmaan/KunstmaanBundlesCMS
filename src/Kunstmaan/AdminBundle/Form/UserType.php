<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Form\AbstractType;

class UserType extends AbstractType
{
	private $container;

	public function __construct(Container $container){
		$this->container = $container;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	// get roles from the service container
    	/*$definedRoles = $this->container->getParameter('security.role_hierarchy.roles');

    	$roles = array();
    	foreach ($definedRoles as $name => $rolesHierarchy) {
    		$roles[$name] = $name . ': ' . implode(', ', $rolesHierarchy);

    		foreach ($rolesHierarchy as $role) {
    			if (!isset($roles[$role])) {
    				$roles[$role] = $role;
    			}
    		}
    	}*/

        $builder->add('username');
        $builder->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => $options['password_required'],
            	'invalid_message' => "The passwords don't match!"));
        $builder->add('email');
        $builder->add('enabled', 'checkbox', array('required' => false));
        $builder->add('groups', null, array(
            'expanded'  => false //change to true to expand to checkboxes
        ));
    }

    public function getName()
    {
        return 'user';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'password_required' => false,
        );
    }

    public function getAllowedOptionValues(array $options)
    {
        return array(
            'password_required' => array(
                true,
                false
            ),
        );
    }
}