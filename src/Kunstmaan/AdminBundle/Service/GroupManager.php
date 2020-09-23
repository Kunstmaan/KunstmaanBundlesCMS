<?php

namespace Kunstmaan\AdminBundle\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\GroupInterface;

class GroupManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    public function __construct(EntityManagerInterface $em, string $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    public function createGroup($name)
    {
        $class = $this->getClass();

        return new $class($name);
    }

    public function deleteGroup(GroupInterface $group)
    {
        $this->em->remove($group);
        $this->em->flush();
    }

    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        $this->em->persist($group);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function getClass()
    {
        return $this->class;
    }

    public function findGroups()
    {
        return $this->repository->findAll();
    }

    public function findGroupByName($name)
    {
        return $this->findGroupBy(['name' => $name]);
    }

    public function findGroupBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
