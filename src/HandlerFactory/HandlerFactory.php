<?php

namespace App\HandlerFactory;

use Psr\Container\ContainerInterface;

/**
 * Class HandlerFactory
 * @package App\HandlerFactory
 */
class HandlerFactory implements HandlerFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * HandlerFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function createHandler(string $handler): HandlerInterface
    {
        return $this->container->get($handler);
    }
}
