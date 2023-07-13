<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Kunstmaan\NodeBundle\Entity\AbstractControllerAction;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Form\ControllerActionAdminType;
use PHPUnit\Framework\TestCase;

class Action extends AbstractControllerAction
{
    public function getPossibleChildTypes()
    {
        return [];
    }

    public function isStructureNode()
    {
        return false;
    }
}

class AbstractControllerActionTest extends TestCase
{
    public function testGetSet()
    {
        $action = new Action();
        $action->setId(5);
        $action->setTitle('Global Economic Meltdown - The Movie');
        /** @var HasNodeInterface $entity */
        $entity = $this->createMock(HasNodeInterface::class);
        $action->setParent($entity);

        $this->assertSame(5, $action->getId());
        $this->assertSame('Global Economic Meltdown - The Movie', $action->getTitle());
        $this->assertInstanceOf($entity::class, $action->getParent());
        $this->assertSame(ControllerActionAdminType::class, $action->getDefaultAdminType());
    }
}
