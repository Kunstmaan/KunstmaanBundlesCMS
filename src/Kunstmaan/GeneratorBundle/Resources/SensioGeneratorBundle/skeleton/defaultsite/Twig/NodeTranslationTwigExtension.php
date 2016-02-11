<?php

namespace {{ namespace }}\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\AbstractPage;

class NodeTranslationTwigExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('get_node_trans_by_node_id', array($this, 'getNodeTranslationByNodeId')),
        );
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
        return 'node_translation_twig_extension';
    }
}
