<?php

namespace Kunstmaan\AdminNodeBundle\Repository;
use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Kunstmaan\AdminNodeBundle\Entity\NodeVersion;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Kunstmaan\AdminBundle\Entity\AddCommand;

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
        return $this->findOneBy(array('refId' => $hasNode->getId(), 'refEntityname' => ClassLookup::getClass($hasNode)));
    }

    /**
     * @param HasNodeInterface $hasNode         The object
     * @param NodeTranslation  $nodeTranslation The nodetranslation
     * @param User             $owner           The user
     * @param string           $type            (public|draft)
     *
     * @throws \Exception
     * @return NodeVersion
     */
    public function createNodeVersionFor(HasNodeInterface $hasNode, NodeTranslation $nodeTranslation, $owner, $type = "public")
    {
        $em = $this->getEntityManager();
        $classname = ClassLookup::getClass($hasNode);
        if (!$hasNode->getId() > 0) {
            throw new \Exception("the entity of class " . $classname . " has no id, maybe you forgot to flush first");
        }
        $entityrepo = $em->getRepository($classname);
        $nodeVersion = new NodeVersion();
        $nodeVersion->setNodeTranslation($nodeTranslation);
        $nodeVersion->setType($type);
        $nodeVersion->setVersion($nodeTranslation->getNodeVersions()->count() + 1);
        $nodeVersion->setOwner($owner);
        $nodeVersion->setRefId($hasNode->getId());
        $nodeVersion->setRefEntityname($classname);

        $addcommand = new AddCommand($em, $owner);
        $addcommand->execute("new version for page \"" . $nodeTranslation->getTitle() . "\" with locale: " . $nodeTranslation->getLang(), array('entity' => $nodeVersion));

        $em->refresh($nodeVersion);

        return $nodeVersion;
    }
}
