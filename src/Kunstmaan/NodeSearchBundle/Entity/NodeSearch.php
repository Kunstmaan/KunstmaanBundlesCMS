<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\Node;
use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Entity()
 * @ORM\Table(name="kuma_nodes_search")
 */
class NodeSearch extends AbstractEntity
{
    /**
     * @var Node
     *
     * @ORM\OneToOne(targetEntity="Kunstmaan\NodeBundle\Entity\Node")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    protected $node;

    /**
     * @var int
     *
     * @ORM\Column(name="boost", type="float", nullable=true)
     */
    protected $boost;

    /**
     * @param float $boost
     */
    public function setBoost($boost)
    {
        $this->boost = $boost;
    }

    /**
     * @return float
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * @param Node $node
     */
    public function setNode(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }
}
