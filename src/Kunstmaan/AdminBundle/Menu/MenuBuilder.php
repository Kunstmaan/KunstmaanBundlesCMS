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

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, Translator $translator, $extra = array())
    {
        $this->factory = $factory;
        $this->rootItem = $this->populateMenu($translator);

        foreach($extra as  $menuadaptor){
            $menuadaptor->adaptMenu($this->rootItem, $translator);
        }
    }

    public function mainMenu(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->rootItem->setCurrentUri($request->getRequestUri());
        return $this->rootItem;
    }

    public function populateMenu(Translator $translator){
        $rootItem = $this->factory->createItem('root');
        $rootItem->getRoot()->setAttribute('class', 'nav');

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