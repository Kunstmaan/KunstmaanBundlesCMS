<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 14/11/11
 * Time: 15:48
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\AdminNodeBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\HasNode;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\AdminBundle\Modules\Slugifier;
// see http://inchoo.net/tools-frameworks/symfony2-event-listeners/

class NodeGenerator {

    public function postUpdate(LifecycleEventArgs $args) {
        $this->updateNode($args);
    }

    public function postPersist(LifecycleEventArgs $args) {
    }

    public function updateNode(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $classname = ClassLookup::getClass($entity);
        if($entity instanceof HasNode){
            $entityrepo = $em->getRepository($classname);
            $nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->getNodeVersionFor($entity);
            if($nodeVersion!=null){
            	$nodeTranslation = $nodeVersion->getNodeTranslation();
            	if($nodeTranslation->getPublicNodeVersion() && $nodeTranslation->getPublicNodeVersion()->getId() == $nodeVersion->getId()){
            		$nodeTranslation->setTitle($entity->__toString());
            		$nodeTranslation->setSlug(Slugifier::slugify($entity->__toString()));
            		$nodeTranslation->setOnline($entity->isOnline());
            		$em->persist($nodeTranslation);
            		$em->flush();
            	}
            }
        }
    }

    public function prePersist(LifecycleEventArgs $args) {

    }

    public function preRemove(LifecycleEventArgs $args) {
    	/*$entity = $args->getEntity();
    	$em = $args->getEntityManager();
    	$classname = ClassLookup::getClass($entity);
    	if($entity instanceof HasNode){
    		$entityrepo = $em->getRepository($classname);
    		$node = $this->getNode($em, $entity->getId(), $classname);
    		$em->remove($node);
    	}*/
    }

    public function postLoad(LifecycleEventArgs $args) {
    	$entity = $args->getEntity();
    	$em = $args->getEntityManager();
    	$classname = ClassLookup::getClass($entity);
    	if($entity instanceof HasNode){
    		$nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->findOneBy(array('refId' => $entity->getId(), 'refEntityname' => $classname));
    		if($nodeVersion){
    			$nodeTranslation = $nodeVersion->getNodeTranslation();
    			$node = $nodeTranslation->getNode();
    			$parentNode = $node->getParent();
    			if($parentNode){
    				$parentNodeTranslation = $parentNode->getNodeTranslation($nodeTranslation->getLang());
    				if($parentNodeTranslation){
    					$parentNodeVersion = $parentNodeTranslation->getPublicNodeVersion();
    					$parent = $em->getRepository($parentNode->getRefEntityname())->find($parentNodeVersion->getRefId());
    					$entity->setParent($parent);
    				}
    			}
    		}
    	}
    }

}