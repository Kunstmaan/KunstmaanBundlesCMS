<?php

namespace Kunstmaan\Rest\NodeBundle\EventSubscriber;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\Rest\NodeBundle\Model\ApiEntity;
use Kunstmaan\Rest\NodeBundle\Model\ApiPagePart;
use Kunstmaan\Rest\NodeBundle\Service\Mapping\InterfaceImplementationMapper;

/**
 * Class SerializerSubscriber
 */
class SerializerSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ['event' => Events::PRE_DESERIALIZE, 'method' => 'replaceInterfaceWithImplementation'],
        ];
    }

    /**
     * @param PreDeserializeEvent $event
     */
    public function replaceInterfaceWithImplementation(PreDeserializeEvent $event)
    {
        $type = $event->getType();
        $object = $event->getContext()->getVisitor()->getCurrentObject();

        if ($object instanceof ApiEntity && $type['name'] === EntityInterface::class) {
            $event->setType($object->getType(), $type['params']);
        }
        if ($object instanceof ApiPagePart && $type['name'] === PagePartInterface::class) {
            $event->setType($object->getType(), $type['params']);
        }
    }
}
