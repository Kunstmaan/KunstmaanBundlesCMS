<?php

namespace {{ namespace }}\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BikesTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
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
        new TwigFunction('get_bikes', array($this, 'getBikes')),
        new TwigFunction('get_submenu_items', array($this, 'getSubmenuItems')),
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
