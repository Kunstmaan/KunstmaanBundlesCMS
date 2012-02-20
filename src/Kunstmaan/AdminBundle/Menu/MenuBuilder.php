<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminBundle\Menu;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder
{
    private $factory;
    private $rootItem;
    private $translator;
    private $extra;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, Translator $translator, $extra = array())
    {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->rootItem = $this->populateMenu($translator);
        $this->extra = $extra;
        
        foreach($extra as  $menuadaptor){
            $menuadaptor->adaptMenu($this->rootItem, $translator);
        }
    }

    public function mainMenu(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->rootItem->setCurrentUri($request->getRequestUri());
        switch(true) {
        	case (stripos($request->attributes->get('_route'), "KunstmaanAdminBundle_pages") === 0):
        		$this->rootItem[$this->translator->trans('pages.title')]->setCurrent(true);
        		break;
        	case (stripos($request->attributes->get('_route'), "KunstmaanAdminBundle_settings") === 0):
        		$this->rootItem[$this->translator->trans('settings.title')]->setCurrent(true);
        		break;
        }     
        foreach($this->extra as  $menuadaptor){
        	$menuadaptor->setCurrent($this->rootItem, $this->translator, $request);
        }   
        return $this->rootItem;
    }

    public function populateMenu(Translator $translator){
        $rootItem = $this->factory->createItem('root');
        $rootItem->getRoot()->setChildrenAttribute('class', 'nav');

        $rootItem->addChild($translator->trans('pages.title'), array( 'route' => 'KunstmaanAdminBundle_pages' ));
        //$rootItem->addChild($translator->trans('modules.title'), array( 'route' => 'KunstmaanAdminBundle_modules'));
        $rootItem->addChild($translator->trans('settings.title'), array( 'route' => 'KunstmaanAdminBundle_settings'));
        //$rootItem->addChild($translator->trans('tools.title'), array('uri' => '#', 'attributes' => array('class' => 'dropdown'), 'linkAttributes' => array('class' => 'dropdown-toggle'), 'childrenAttributes' => array('class' => 'dropdown-menu')));

            //$rootItem[$translator->trans('tools.title')]->addChild($translator->trans('tools.clear_frontend_cache'), array( 'uri' => '#'));
            //$rootItem[$translator->trans('tools.title')]->addChild($translator->trans('tools.clear_backend_cache'), array( 'uri' => '#'));
            //$rootItem[$translator->trans('tools.title')]->addChild($translator->trans('tools.clear_all_caches'), array( 'uri' => '#'));
            //$rootItem[$translator->trans('tools.title')]->addChild('', array('attributes' => array('class' => 'divider')));
            //$rootItem[$translator->trans('tools.title')]->addChild($translator->trans('tools.shutdown'), array( 'uri' => '#'));
        
        return $rootItem;
    }
}