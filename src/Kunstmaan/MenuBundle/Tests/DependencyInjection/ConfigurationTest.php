<?php

namespace Kunstmaan\MenuBundle\Tests\DependencyInjection;

use Kunstmaan\MenuBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit_Framework_TestCase;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'menus' => [],
            'menu_entity' => 'Kunstmaan\MenuBundle\Entity\Menu',
            'menuitem_entity' => 'Kunstmaan\MenuBundle\Entity\MenuItem',
            'menu_adminlist' => 'Kunstmaan\MenuBundle\AdminList\MenuAdminListConfigurator',
            'menuitem_adminlist' => 'Kunstmaan\MenuBundle\AdminListConfigurator',
            'menu_form' => 'Kunstmaan\MenuBundle\Form\MenuAdminType',
            'menuitem_form' => 'Kunstmaan\MenuBundle\Form\MenuItemAdminType',
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
