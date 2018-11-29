<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Class MappingListener
 */
class MappingListener
{
    /**
     * @var string
     */
    private $className;

    /**
     * Constructor
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Called when class meta data is fetched.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $entityName = (string) $classMetadata->getName();

        // We dynamically set the user class that was configured in the configuration
        if ($entityName == 'Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction') {
            $mapping = array(
                'fieldName' => 'user',
                'targetEntity' => $this->className,
                'joinColumns' => array(array(
                    'name' => 'user_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => true,
                )),
            );
            $classMetadata->mapManyToOne($mapping);
        }
    }
}
