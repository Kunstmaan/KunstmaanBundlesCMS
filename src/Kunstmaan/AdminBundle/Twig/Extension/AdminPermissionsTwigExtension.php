<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;

class AdminPermissionsTwigExtension extends \Twig_Extension
{
    /* @var \Twig_Environment */
    protected $environment;

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(\Twig_Environment $environment)
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
     * @param \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin $permissionAdmin
     * @param Form                                                                  $form
     * @param array                                                                 $parameters
     *
     * @return string
     */
    public function renderWidget(PermissionAdmin $permissionAdmin, Form $form , array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanAdminBundle:PermissionsAdminTwigExtension:widget.html.twig");

        return $template->render(array_merge(array(
            'form'              => $form,
            'permissionadmin'   => $permissionAdmin,
            'recursiveSupport'  => true
        ), $parameters));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'permissionsadmin_twig_extension';
    }
}
