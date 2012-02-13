<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\FormBundle\Helper\Menu;
use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class FormSubmissionsMenuAdaptor implements \Kunstmaan\AdminBundle\Menu\MenuAdaptorInterface {
	public function setCurrent(KnpMenu $menu, Translator $translator, $request) {
		switch (true) {
		case (stripos($request->attributes->get('_route'), "KunstmaanFormBundle") === 0):
			$menu[$translator->trans('formsubmissions.menu.title')]->setCurrent(true);
			break;
		}
	}

	public function adaptMenu(KnpMenu $menu, Translator $translator) {
		$menu->addChild($translator->trans('formsubmissions.menu.title'), array('route' => 'KunstmaanFormBundle_formsubmissions'));
		$menu[$translator->trans('formsubmissions.menu.title')]->moveToPosition(3);
	}

}
