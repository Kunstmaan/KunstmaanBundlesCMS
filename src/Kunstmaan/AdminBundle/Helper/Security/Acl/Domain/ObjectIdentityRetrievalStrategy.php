<?php
namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Domain;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\InvalidDomainObjectException;
use Symfony\Component\Security\Acl\Model\DomainObjectInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

use Doctrine\ORM\EntityManager;

/**
 * Strategy to be used for retrieving object identities from domain objects
 * where the domain object may be a Doctrine proxy.
 *
 * @link http://jonathaningram.com.au/2012/01/13/overriding-the-objectidentityretrievalstrategy-to-check-if-a-domain-object-is-a-doctrine-proxy/
 * @link http://stackoverflow.com/questions/7476552/doctrine-2-proxy-classes-breaking-symfony2-acl
 *
 * NOTE: This is only needed for Symfony 2.0, in Symfony 2.1 this should no longer be necessary!
 *
 * @deprecated
 */
class ObjectIdentityRetrievalStrategy implements ObjectIdentityRetrievalStrategyInterface
{
    /**
     * @var EntityManager $em
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getObjectIdentity($domainObject)
    {
        try {
            if ($domainObject instanceof \Doctrine\ORM\Proxy\Proxy) {
                return $this->fromDomainObject($domainObject);
            }

            return ObjectIdentity::fromDomainObject($domainObject);
        } catch (InvalidDomainObjectException $failed) {
            return null;
        }
    }

    private function fromDomainObject($domainObject)
    {
        if (!is_object($domainObject)) {
            throw new InvalidDomainObjectException('$domainObject must be an object.');
        }

        try {
            if ($domainObject instanceof DomainObjectInterface) {
                return new ObjectIdentity($domainObject->getObjectIdentifier(), $this->em->getClassMetadata(get_class($domainObject))->getName());
            } elseif (method_exists($domainObject, 'getId')) {
                return new ObjectIdentity($domainObject->getId(), $this->em->getClassMetadata(get_class($domainObject))->getName());
            }
        } catch (\InvalidArgumentException $invalid) {
            throw new InvalidDomainObjectException($invalid->getMessage(), 0, $invalid);
        }

        throw new InvalidDomainObjectException('$domainObject must either implement the DomainObjectInterface, or have a method named "getId".');
    }
}
