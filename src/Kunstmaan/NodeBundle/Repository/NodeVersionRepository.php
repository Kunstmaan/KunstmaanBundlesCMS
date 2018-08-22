<?php

namespace Kunstmaan\NodeBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * NodeRepository
 */
class NodeVersionRepository extends EntityRepository
{
    /**
     * @param HasNodeInterface $hasNode
     *
     * @return NodeVersion
     */
    public function getNodeVersionFor(HasNodeInterface $hasNode)
    {
        return $this->findOneBy(
            array(
                'refId'         => $hasNode->getId(),
                'refEntityName' => ClassLookup::getClass($hasNode)
            )
        );
    }

    /**
     * @param HasNodeInterface $hasNode         The object
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param BaseUser         $owner           The user
     * @param NodeVersion      $origin          The nodeVersion this nodeVersion originated from
     * @param string           $type            (public|draft)
     * @param DateTime         $created         The date this node version is created
     *
     * @return NodeVersion
     */
    public function createNodeVersionFor(
        HasNodeInterface $hasNode,
        NodeTranslation $nodeTranslation,
        BaseUser $owner,
        NodeVersion $origin = null,
        $type = 'public',
        $created = null
    ) {
        $em = $this->getEntityManager();

        $nodeVersion = new NodeVersion();
        $nodeVersion->setNodeTranslation($nodeTranslation);
        $nodeVersion->setType($type);
        $nodeVersion->setOwner($owner);
        $nodeVersion->setRef($hasNode);
        $nodeVersion->setOrigin($origin);

        if (!is_null($created)) {
            $nodeVersion->setCreated($created);
        }

        $em->persist($nodeVersion);
        $em->flush();
        $em->refresh($nodeVersion);

        return $nodeVersion;
    }
}
