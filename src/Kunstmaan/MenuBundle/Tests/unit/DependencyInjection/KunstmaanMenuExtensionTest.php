<?php

namespace Kunstmaan\MenuBundle\Tests\DependencyInjection;

use Kunstmaan\MenuBundle\DependencyInjection\KunstmaanMenuExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanMenuExtensionTest
 */
class KunstmaanMenuExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanMenuExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_menu.menus');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.entity.menu.class', 'Kunstmaan\MenuBundle\Entity\Menu');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.entity.menuitem.class', 'Kunstmaan\MenuBundle\Entity\MenuItem');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.adminlist.menu_configurator.class', 'Kunstmaan\MenuBundle\AdminList\MenuAdminListConfigurator');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.adminlist.menuitem_configurator.class', 'Kunstmaan\MenuBundle\AdminList\MenuItemAdminListConfigurator');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.form.menu_admintype.class', 'Kunstmaan\MenuBundle\Form\MenuAdminType');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.form.menuitem_admintype.class', 'Kunstmaan\MenuBundle\Form\MenuItemAdminType');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.menu.adaptor.class', 'Kunstmaan\MenuBundle\Service\MenuAdaptor');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.menu.service.class', 'Kunstmaan\MenuBundle\Service\MenuService');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.menu.twig.extension.class', 'Kunstmaan\MenuBundle\Twig\MenuTwigExtension');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.menu.repository.class', 'Kunstmaan\MenuBundle\Repository\MenuItemRepository');
        $this->assertContainerBuilderHasParameter('kunstmaan_menu.menu.render_service.class', 'Kunstmaan\MenuBundle\Service\RenderService');
    }
}
