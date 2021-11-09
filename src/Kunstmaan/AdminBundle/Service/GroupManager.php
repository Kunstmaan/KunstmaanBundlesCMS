<?php

namespace Kunstmaan\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\GroupInterface;

class GroupManager
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var string */
    private $class;
    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em, string $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $class;
    }

    public function createGroup($name): GroupInterface
    {
        $class = $this->getClass();

        return new $class($name);
    }

    public function deleteGroup(GroupInterface $group): void
    {
        $this->em->remove($group);
        $this->em->flush();
    }

    public function updateGroup(GroupInterface $group, $andFlush = true): void
    {
        $this->em->persist($group);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function findGroups(): array
    {
        return $this->repository->findAll();
    }

    public function findGroupByName($name): ?GroupInterface
    {
        return $this->findGroupBy(['name' => $name]);
    }

    private function findGroupBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
