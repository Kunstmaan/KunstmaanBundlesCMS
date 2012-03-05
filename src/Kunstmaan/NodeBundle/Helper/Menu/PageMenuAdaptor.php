<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminNodeBundle\Helper\Menu;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class PageMenuAdaptor implements \Kunstmaan\AdminBundle\Menu\MenuAdaptorInterface
{
    public function setCurrent(KnpMenu $menu, Translator $translator, $request){
    	switch(true) {
        	case (stripos($request->attributes->get('_route'), "KunstmaanAdminBundle_pages") === 0):
        		$menu[$translator->trans('pages.title')]->setCurrent(true);
        		break;
        }    
    }
	
	public function adaptMenu(KnpMenu $menu, Translator $translator)
    {
    	$menu->addChild($translator->trans('pages.title'), array( 'route' => 'KunstmaanAdminBundle_pages' ));
    	$menu[$translator->trans('pages.title')]->moveToPosition(0);
    }

}