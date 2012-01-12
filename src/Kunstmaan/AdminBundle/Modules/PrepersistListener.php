<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminBundle\Modules;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Kunstmaan\AdminNodeBundle\Entity\Node;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class PrepersistListener
{
    private $entities = array();

    public function prePersist(LifecycleEventArgs $args) {
    	$entity = $args->getEntity();
    	$this->entities[] = $entity;
    }

    public function getEntities(){
        return $this->entities;
    }
    
}