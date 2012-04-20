<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder
{
    private $rootItem;
    private $translator;
    private $extra;
    private $request;
    private $adaptors = array();
    private $topmenuitems = null;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(Translator $translator, ContainerInterface $container)
    {
        $this->translator = $translator;
        $this->rootItem = $this->populateMenu($translator);
        $this->container = $container;
    }
    
    public function addAdaptMenu(MenuAdaptorInterface $adaptor)
    {
        $this->adaptors[] = $adaptor;
    }
    
    public function getCurrent()
    {
        $active = null;
        do {
            $children = $this->getChildren($active);
            $foundActiveChild = false;
            foreach($children as $child){
                if($child->getActive()){
                    $foundActiveChild = true;
                    $active = $child;
                    break;
                }
            }
        } while($foundActiveChild);
        return $active;
    }
    
    public function getBreadCrumb()
    {
        $result = array();
        $current = $this->getCurrent();
        while(!is_null($current)){
            array_unshift($result, $current);
            $current = $current->getParent();
        }
        return $result;
    }
    
    public function getLowestTopChild(){
        $current = $this->getCurrent();
        while(!is_null($current)){
            if($current instanceof TopMenuItem){
                return $current;
            }
            $current = $current->getParent();
        }
        return null;
    }
    
    public function getTopChildren(){
        $request = $this->container->get('request');
        if(is_null($this->topmenuitems)){
            $this->topmenuitems = array();
            foreach($this->adaptors as $menuadaptor){
                $adaptions = $menuadaptor->getChildren($this, null, $request);
                if(!is_null($adaptions)){
                    $this->topmenuitems = array_merge($this->topmenuitems, $adaptions);
                }
            }
        }
        return $this->topmenuitems;
    }
    
    public function getChildren(MenuItem $parent = null){
        $request = $this->container->get('request');
        $result = array();
        foreach($this->adaptors as $menuadaptor){
            $adaptions = $menuadaptor->getChildren($this, $parent, $request);
            if(!is_null($adaptions)){
                $result = array_merge($result, $adaptions);
            }
        }
        return $result;
    }

    public function mainMenu(\Symfony\Component\HttpFoundation\Request $request)
    {
        die();
        $this->request = $request;
        return null;
        /*$this->rootItem->setCurrentUri($request->getRequestUri());
        switch(true) {
        	case (stripos($request->attributes->get('_route'), "KunstmaanAdminBundle_settings") === 0):
        		$this->rootItem[$this->translator->trans('settings.title')]->setCurrent(true);
        		break;
        }     
        foreach($this->extra as $menuadaptor){
        	$menuadaptor->setCurrent($this->rootItem, $this->translator, $request);
        }   
        return $this->rootItem;*/
    }

    public function populateMenu(Translator $translator){
        return null;
        /*$rootItem = $this->factory->createItem('root');
        $rootItem->getRoot()->setChildrenAttribute('class', 'nav');

        $rootItem->addChild($translator->trans('settings.title'), array( 'route' => 'KunstmaanAdminBundle_settings'));
        
        return $rootItem;*/
    }
}