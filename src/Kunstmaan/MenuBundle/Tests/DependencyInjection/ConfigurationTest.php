<?php

namespace Kunstmaan\MenuBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Kunstmaan\MenuBundle\Entity\Menu;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use Kunstmaan\MenuBundle\AdminList\MenuAdminListConfigurator;
use Kunstmaan\MenuBundle\Form\MenuItemAdminType;
use Kunstmaan\MenuBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'menus' => [],
            'menu_entity' => Menu::class,
            'menuitem_entity' => MenuItem::class,
            'menu_adminlist' => MenuAdminListConfigurator::class,
            'menuitem_adminlist' => 'Kunstmaan\MenuBundle\AdminListConfigurator',
            'menu_form' => 'Kunstmaan\MenuBundle\Form\MenuAdminType',
            'menuitem_form' => MenuItemAdminType::class,
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
