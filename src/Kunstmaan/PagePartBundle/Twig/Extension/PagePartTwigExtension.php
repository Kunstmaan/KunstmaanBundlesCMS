<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;

class PagePartTwigExtension extends \Twig_Extension
{

    protected $em;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'getpageparts_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
            'getpageparts'  => new \Twig_Function_Method($this, 'getPageParts'),
        );
    }


    public function renderWidget($page, $context = "main", array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanViewBundle:GetPagepartsTwigExtension:widget.html.twig");

        $pageparts = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $context);

        return $template->render(array_merge($parameters, array(
            'pageparts' => $pageparts
        )));
    }

    public function getPageParts($page, $context = "main")
    {
        $pageparts = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $context);

        return $pageparts;
    }

    public function getName()
    {
        return 'pageparts_twig_extension';
    }

}
