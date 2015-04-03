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
	    'get_bikes' => new \Twig_Function_Method($this, 'getBikes'),
	    'get_submenu_items' => new \Twig_Function_Method($this, 'getSubmenuItems'),
	    'get_node_trans_by_node_id' => new \Twig_Function_Method($this, 'getNodeTranslationByNodeId')
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
     * Get the node translation object based on node id and language.
     *
     * @param int $nodeId
     * @param string $lang
     * @return NodeTranslation
     */
    public function getNodeTranslationByNodeId($nodeId, $lang)
    {
	$repo = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation');
	$qb = $repo->createQueryBuilder('nt')
	    ->select('nt')
	    ->innerJoin('nt.node', 'n', 'WITH', 'nt.node = n.id')
	    ->where('n.deleted != 1')
	    ->andWhere('nt.online = 1')
	    ->andWhere('nt.lang = :lang')
	    ->setParameter('lang', $lang)
	    ->andWhere('n.id = :node_id')
	    ->setParameter('node_id', $nodeId)
	    ->setFirstResult(0)
	    ->setMaxResults(1);

	return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return string
     */
    public function getName()
    {
	return 'bikes_twig_extension';
    }
}
