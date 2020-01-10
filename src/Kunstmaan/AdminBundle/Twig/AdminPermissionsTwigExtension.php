<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * AdminPermissionsTwigExtension
 *
 * @final since 5.4
 */
class AdminPermissionsTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('permissionsadmin_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * Renders the permission admin widget.
     *
     * @param Environment     $env
     * @param PermissionAdmin $permissionAdmin The permission admin
     * @param FormView        $form            The form
     * @param array           $parameters      Extra parameters
     *
     * @return string
     */
    public function renderWidget(Environment $env, PermissionAdmin $permissionAdmin, FormView $form, array $parameters = array())
    {
        $template = $env->load('@KunstmaanAdmin/PermissionsAdminTwigExtension/widget.html.twig');

        return $template->render(array_merge(array(
            'form' => $form,
            'permissionadmin' => $permissionAdmin,
            'recursiveSupport' => true,
        ), $parameters));
    }
}
