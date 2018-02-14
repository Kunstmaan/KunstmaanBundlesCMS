<?php

namespace Kunstmaan\NodeBundle\Helper\NodeAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersionLock;
use Kunstmaan\NodeBundle\Repository\NodeVersionLockRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NodeVersionLockHelper
 *
 * @package Kunstmaan\NodeBundle\Helper\NodeAdmin
 */
class NodeVersionLockHelper
{
    /** @var ContainerInterface */
    private $container;

    /** @var EntityManagerInterface */
    private $em;

    /** @var string|null */
    private $threshold;

    /**
     * NodeVersionLockHelper constructor.
     *
     * @param EntityManagerInterface|ContainerInterface|null $em
     * @param string                      $threshold
     */
    public function __construct(/* EntityManagerInterface */ $em = null, $threshold = null)
    {
        if ($em instanceof ContainerInterface) {
            @trigger_error(
                'Container injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
                E_USER_DEPRECATED
            );

            $this->container = $em;
            $this->em = $em->get(EntityManagerInterface::class);
            $this->threshold = $em->getParameter('kunstmaan_node.lock_threshold');

            return;
        }

        $this->threshold = $threshold;
        $this->em = $em;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container)
    {
        @trigger_error(
            'Container injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->em = $container->get(EntityManagerInterface::class);
        $this->threshold = $container->getParameter('kunstmaan_node.lock_threshold');
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setObjectManager(EntityManagerInterface $em)
    {
        @trigger_error(
            'Setter injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->em = $em;
    }

    /**
     * @param BaseUser        $user
     * @param NodeTranslation $nodeTranslation
     * @param bool            $isPublicNodeVersion
     *
     * @return bool
     */
    public function isNodeVersionLocked(BaseUser $user, NodeTranslation $nodeTranslation, $isPublicNodeVersion)
    {
        if ($this->threshold) {
            $this->removeExpiredLocks($nodeTranslation);
            $this->createNodeVersionLock($user, $nodeTranslation, $isPublicNodeVersion); // refresh lock
            $locks = $this->getNodeVersionLocksByNodeTranslation($nodeTranslation, $isPublicNodeVersion, $user);

            return \count($locks) ? true : false;
        }

        return false;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     * @param BaseUser        $userToExclude
     * @param bool            $isPublicNodeVersion
     *
     * @return array
     */
    public function getUsersWithNodeVersionLock(NodeTranslation $nodeTranslation, $isPublicNodeVersion, BaseUser $userToExclude = null)
    {
        return array_reduce(
            $this->getNodeVersionLocksByNodeTranslation($nodeTranslation, $isPublicNodeVersion, $userToExclude),
            function ($return, NodeVersionLock $item) {
                $return[] = $item->getOwner();

                return $return;
            },
            []
        );
    }

    /**
     * @param NodeTranslation $nodeTranslation
     */
    protected function removeExpiredLocks(NodeTranslation $nodeTranslation)
    {
        $locks = $this->em->getRepository('KunstmaanNodeBundle:NodeVersionLock')->getExpiredLocks($nodeTranslation, $this->threshold);
        foreach ($locks as $lock) {
            $this->em->remove($lock);
        }
    }

    /**
     * When editing the node, create a new node translation lock.
     *
     * @param BaseUser        $user
     * @param NodeTranslation $nodeTranslation
     * @param bool            $isPublicVersion
     */
    protected function createNodeVersionLock(BaseUser $user, NodeTranslation $nodeTranslation, $isPublicVersion)
    {
        $lock = $this->em->getRepository('KunstmaanNodeBundle:NodeVersionLock')->findOneBy(
            [
                'owner' => $user->getUsername(),
                'nodeTranslation' => $nodeTranslation,
                'publicVersion' => $isPublicVersion,
            ]
        );
        if (!$lock) {
            $lock = new NodeVersionLock();
        }
        $lock->setOwner($user->getUsername());
        $lock->setNodeTranslation($nodeTranslation);
        $lock->setPublicVersion($isPublicVersion);
        $lock->setCreatedAt(new \DateTime());

        $this->em->persist($lock);
        $this->em->flush();
    }

    /**
     * When editing a node, check if there is a lock for this node translation.
     *
     * @param NodeTranslation $nodeTranslation
     * @param bool            $isPublicVersion
     * @param BaseUser        $userToExclude
     *
     * @return NodeVersionLock[]
     */
    protected function getNodeVersionLocksByNodeTranslation(NodeTranslation $nodeTranslation, $isPublicVersion, BaseUser $userToExclude = null)
    {
        /** @var NodeVersionLockRepository $objectRepository */
        $objectRepository = $this->em->getRepository('KunstmaanNodeBundle:NodeVersionLock');

        return $objectRepository->getLocksForNodeTranslation($nodeTranslation, $isPublicVersion, $this->threshold, $userToExclude);
    }
}
