<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;


class AdminPermissionsTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions() {
        return array(
            'permissionsadmin_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
        );
    }


    public function renderWidget($permissionadmin , $form , array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanAdminBundle:PermissionsAdminTwigExtension:widget.html.twig");
        return $template->render(array_merge($parameters, array(
            'form'              => $form,
            'permissionadmin'   => $permissionadmin
        )));
    }

    public function getName()
    {
        return 'permissionsadmin_twig_extension';
    }
}
