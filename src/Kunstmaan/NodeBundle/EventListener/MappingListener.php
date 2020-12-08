<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class MappingListener
{
    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Called when class meta data is fetched.
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $entityName = (string) $classMetadata->getName();

        // We dynamically set the user class that was configured in the configuration
        if ($entityName == 'Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction') {
            $mapping = [
                'fieldName' => 'user',
                'targetEntity' => $this->className,
                'joinColumns' => [[
                    'name' => 'user_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => true,
                ]],
            ];
            $classMetadata->mapManyToOne($mapping);
        }
    }
}
