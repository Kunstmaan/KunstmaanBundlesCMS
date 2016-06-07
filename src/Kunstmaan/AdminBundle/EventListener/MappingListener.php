<?php

namespace Kunstmaan\AdminBundle\EventListener;

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
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $entityName = strval($classMetadata->getName());

        // We dynamically set the user class that was configured in the configuration
        if ($entityName == 'Kunstmaan\AdminBundle\Entity\AclChangeset') {
            $mapping = array(
                'fieldName' => 'user',
                'targetEntity' => $this->className,
                'joinColumns' => array(array(
                    'name' => 'user_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => true
                )),
            );
            $classMetadata->mapManyToOne($mapping);
        }

        // If we overwrite the user entity, we should create a new join table with the groups
        if ($entityName == $this->className) {
            $tableName = $classMetadata->table['name'];
            $mapping = array(
                'fieldName' => 'groups',
                'joinTable' => array(
                    'name' => $tableName . '_group',
                    'joinColumns' => array(array(
                        'name' => 'user_id',
                        'unique' => false,
                        'nullable' => true,
                        'referencedColumnName' => 'id'
                    )),
                    'inverseJoinColumns' => array(array(
                        'name' => 'group_id',
                        'unique' => false,
                        'nullable' => true,
                        'referencedColumnName' => 'id'
                    ))
                ),
                'targetEntity' => 'Zizoo\UserBundle\Entity\Group',
                'sourceEntity' => $this->className
            );
            $classMetadata->mapManyToMany($mapping);
        }
    }
}
