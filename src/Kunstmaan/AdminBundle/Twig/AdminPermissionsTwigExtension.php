<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Symfony\Component\Form\FormView;
use Twig_Environment;

/**
 * AdminPermissionsTwigExtension
 */
class AdminPermissionsTwigExtension extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('permissionsadmin_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * Renders the permission admin widget.
     *
     * @param \Twig_Environment $env
     * @param PermissionAdmin   $permissionAdmin The permission admin
     * @param FormView          $form            The form
     * @param array             $parameters      Extra parameters
     *
     * @return string
     */
    public function renderWidget(Twig_Environment $env, PermissionAdmin $permissionAdmin, FormView $form, array $parameters = array())
    {
        $template = $env->loadTemplate('KunstmaanAdminBundle:PermissionsAdminTwigExtension:widget.html.twig');

        return $template->render(array_merge(array(
            'form' => $form,
            'permissionadmin' => $permissionAdmin,
            'recursiveSupport' => true,
        ), $parameters));
    }
}
