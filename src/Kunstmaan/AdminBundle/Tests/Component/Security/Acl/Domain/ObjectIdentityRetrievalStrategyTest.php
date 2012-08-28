<?php

namespace Kunstmaan\AdminBundle\Tests\Component\Security\Acl\Domain;

use Kunstmaan\AdminBundle\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;

class ObjectIdentityRetrievalStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetObjectIdentityReturnsNullForInvalidDomainObject()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $strategy = new ObjectIdentityRetrievalStrategy($em);
        $this->assertNull($strategy->getObjectIdentity('foo'));
    }

    public function testGetObjectIdentityForDomainObject()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $strategy = new ObjectIdentityRetrievalStrategy($em);
        $domainObject = new DomainObject();
        $objectIdentity = $strategy->getObjectIdentity($domainObject);
        $this->assertEquals($domainObject->getId(), $objectIdentity->getIdentifier());
        $this->assertEquals(get_class($domainObject), $objectIdentity->getType());
    }

    /*
     * @todo add unit test for entity using proxy
    public function testFromDomainObjectWithProxy()
    {
        $this->assertEquals(true, true);
    }
    */
}

class DomainObject
{
    public function getId()
    {
        return 'foo';
    }

}
