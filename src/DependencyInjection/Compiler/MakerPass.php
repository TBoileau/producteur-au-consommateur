<?php

namespace App\DependencyInjection\Compiler;

use App\HandlerFactory\HandlerFactory;
use App\Maker\HandlerMaker;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MakerPass
 * @package App\DependencyInjection\Compiler
 */
class MakerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(HandlerMaker::class);

        $formTypes = [];

        $taggedServices = $container->findTaggedServiceIds("form.type", true);

        foreach (array_keys($taggedServices) as $serviceId) {
            $entityClassDetails = new ClassNameDetails($container->getDefinition($serviceId)->getClass(), "App\Form");
            $formTypes[$entityClassDetails->getRelativeName()] = $serviceId;
        }

        $definition->setArgument(0, $formTypes);
    }
}
