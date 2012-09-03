<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of GuestUserFactory
 *
 * @author wim
 */
class GuestUserFactory implements SecurityFactoryInterface
{
    protected $options = array(
        'username' => 'guest',
    );

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        // We don't need a custom provider yet, so use pre-authenticated provider...
        $providerId = 'security.authentication.provider.pre_authenticated.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.pre_authenticated'))
            ->replaceArgument(0, new Reference($userProvider))
            ->addArgument($id)
        ;

        $listenerId = 'security.authentication.listener.kunstmaan.guest.'.$id;
        $listener = $container
            ->setDefinition($listenerId, new DefinitionDecorator('kunstmaan.guest.security.authentication.listener'))
            ->replaceArgument(1, new Reference($userProvider))
            ->replaceArgument(2, $id)
        ;

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function addConfiguration(NodeDefinition $node)
    {

        $builder = $node->children();

        foreach ($this->options as $name => $default) {
            if (is_bool($default)) {
                $builder->booleanNode($name)->defaultValue($default);
            } else {
                $builder->scalarNode($name)->defaultValue($default);
            }
        }
    }

    public function getKey()
    {
        return 'guest-user';
    }

    public function getPosition()
    {
        return 'pre_auth';
    }
}
