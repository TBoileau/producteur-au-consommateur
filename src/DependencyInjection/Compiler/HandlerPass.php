<?php

namespace App\DependencyInjection\Compiler;

use App\HandlerFactory\HandlerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class HandlerPass
 * @package App\DependencyInjection\Compiler
 */
class HandlerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(HandlerFactory::class);

        $serviceMap = [];

        $taggedServices = $container->findTaggedServiceIds("app.handler", true);

        foreach (array_keys($taggedServices) as $serviceId) {
            $serviceMap[$container->getDefinition($serviceId)->getClass()] = new Reference($serviceId);
        }

        $definition->setArgument(0, ServiceLocatorTagPass::register($container, $serviceMap));
    }
}
