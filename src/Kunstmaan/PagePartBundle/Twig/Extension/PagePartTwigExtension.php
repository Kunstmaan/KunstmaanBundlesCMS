<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;

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

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'getpageparts_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
            'getpageparts'  => new \Twig_Function_Method($this, 'getPageParts'),
        );
    }

    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param string                                         $context
     * @param array                                          $parameters
     *
     * @return string
     */
    public function renderWidget(AbstractPage $page, $context = "main", array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanViewBundle:GetPagepartsTwigExtension:widget.html.twig");
        /** @var $entityRepository PagePartRefRepository */
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pageparts = $entityRepository->getPageParts($page, $context);

        return $template->render(array_merge($parameters, array(
            'pageparts' => $pageparts
        )));
    }

    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param string                                         $context
     *
     * @return PagePartInterface[]
     */
    public function getPageParts(AbstractPage $page, $context = "main")
    {
        /** @var $entityRepository PagePartRefRepository */
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pageparts = $entityRepository->getPageParts($page, $context);

        return $pageparts;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pageparts_twig_extension';
    }

}
