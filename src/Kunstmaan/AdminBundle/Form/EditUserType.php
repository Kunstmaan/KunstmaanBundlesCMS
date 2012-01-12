<?php
// src/Blogger/BlogBundle/Form/EnquiryType.php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EditUserType extends AbstractType
{
	private $container;
	
	public function __construct(Container $container){
		$this->container = $container;
	}
	
    public function buildForm(FormBuilder $builder, array $options)
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
        		'required' => false,
            	'invalid_message' => "The passwords don't match!"));
        $builder->add('email');
        $builder->add('enabled', 'checkbox', array('required' => false));

        $builder->add('groups', null, array(
            'expanded'  => false //change to true to expand to checkboxes
        ));

    }

    public function getName()
    {
        return 'edituser';
    }
}