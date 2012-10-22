<?php

namespace Kunstmaan\NodeBundle\Repository;

use DateTime;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

use Doctrine\ORM\EntityRepository;

/**
 * NodeRepository
 *
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
        return $this->findOneBy(array('refId' => $hasNode->getId(), 'refEntityName' => ClassLookup::getClass($hasNode)));
    }

    /**
     * @param HasNodeInterface $hasNode         The object
     * @param NodeTranslation  $nodeTranslation The nodetranslation
     * @param User             $owner           The user
     * @param NodeVersion      $origin          The nodeVersion this nodeVersion originated from
     * @param string           $type            (public|draft)
     * @param DateTime         $created         The date this nodeversion is created
     *
     * @return NodeVersion
     */
    public function createNodeVersionFor(HasNodeInterface $hasNode, NodeTranslation $nodeTranslation, $owner, NodeVersion $origin = null, $type = "public", $created = null)
    {
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
