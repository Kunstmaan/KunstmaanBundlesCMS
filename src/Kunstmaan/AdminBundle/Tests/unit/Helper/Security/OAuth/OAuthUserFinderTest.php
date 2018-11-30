<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\OAuth\OAuthUserFinder;
use PHPUnit_Framework_TestCase;

class OAuthUserFinderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OAuthUserFinder
     */
    private $object;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEm()
    {
        if (!isset($this->em)) {
            $this->em = $this->createMock(EntityManager::class);
        }

        return $this->em;
    }

    public function setup()
    {
        $em = $this->getEm();
        $object = new OAuthUserFinder($em, User::class);
        $this->object = $object;
    }

    public function testFindUserByGoogleSignInData()
    {
        $object = $this->object;
        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->exactly(3))->method('findOneBy')->will($this->onConsecutiveCalls(new DateTime(), new DateTime(), new User()));
        $this->getEm()->expects($this->exactly(3))->method('getRepository')->willReturn($repo);
        $user = $object->findUserByGoogleSignInData('someone@gmail.com', 12345679);
        $this->assertInstanceOf(User::class, $user);
    }
}
