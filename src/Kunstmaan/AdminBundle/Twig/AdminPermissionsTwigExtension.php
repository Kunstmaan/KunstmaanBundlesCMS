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
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'permissionsadmin_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders the permission admin widget.
     *
     * @param PermissionAdmin $permissionAdmin The permission admin
     * @param FormView        $form            The form
     * @param array           $parameters      Extra parameters
     *
     * @return string
     */
    public function renderWidget(PermissionAdmin $permissionAdmin, FormView $form , array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanAdminBundle:PermissionsAdminTwigExtension:widget.html.twig");

        return $template->render(array_merge(array(
            'form'              => $form,
            'permissionadmin'   => $permissionAdmin,
            'recursiveSupport'  => true
        ), $parameters));
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'permissionsadmin_twig_extension';
    }
}
