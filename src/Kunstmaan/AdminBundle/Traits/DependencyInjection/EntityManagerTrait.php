<?php

namespace  Kunstmaan\AdminBundle\Traits\DependencyInjection;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerTrait
 */
trait EntityManagerTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * You can override this method to return the correct entity manager when using multiple databases ...
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEntityManager()
    {
        if (null !== $this->container && null === $this->entityManager) {
            $this->entityManager = $this->container->get('doctrine')->getManager();
        }
        return $this->entityManager;
    }
}
