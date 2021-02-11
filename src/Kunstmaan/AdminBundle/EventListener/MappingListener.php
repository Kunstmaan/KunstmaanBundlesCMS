<?php

namespace Kunstmaan\AdminBundle\EventListener;

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
        if ($entityName == 'Kunstmaan\AdminBundle\Entity\AclChangeset') {
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

        // If we overwrite the user entity, we should create a new join table with the groups
        if ($entityName == $this->className) {
            $tableName = $classMetadata->table['name'];
            $mapping = [
                'fieldName' => 'groups',
                'joinTable' => [
                    'name' => $tableName . '_groups',
                    'joinColumns' => [[
                        'name' => 'user_id',
                        'unique' => false,
                        'nullable' => true,
                        'referencedColumnName' => 'id',
                    ]],
                    'inverseJoinColumns' => [[
                        'name' => 'group_id',
                        'unique' => false,
                        'nullable' => true,
                        'referencedColumnName' => 'id',
                    ]],
                ],
                'targetEntity' => 'Kunstmaan\AdminBundle\Entity\Group',
                'sourceEntity' => $this->className,
            ];
            $classMetadata->mapManyToMany($mapping);
        }
    }
}
