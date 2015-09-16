<?php

namespace {{ namespace }}\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\AbstractPage;

class BikesTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
	$this->em = $em;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
	return array(
        new \Twig_SimpleFunction('get_bikes', array($this, 'getBikes')),
        new \Twig_SimpleFunction('get_submenu_items', array($this, 'getSubmenuItems')),
	);
    }

    /**
     * @return array
     */
    public function getBikes()
    {
	return $this->em->getRepository('{{ bundle.getName() }}:Bike')->findAll();
    }

    /**
     * @param AbstractPage $page
     * @param string $locale
     * @return array
     */
    public function getSubmenuItems(AbstractPage $page, $locale)
    {
	$items = array();

	$nv = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->getNodeVersionFor($page);
	if ($nv) {
	    $nodeTranslations = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getOnlineChildren($nv->getNodeTranslation()->getNode(), $locale);
	    foreach ($nodeTranslations as $nt) {
		$childPage = $nt->getPublicNodeVersion()->getRef($this->em);
		$items[] = array('nt' => $nt, 'page' => $childPage);
	    }
	}

	return $items;
    }

    /**
     * @return string
     */
    public function getName()
    {
	return 'bikes_twig_extension';
    }
}
