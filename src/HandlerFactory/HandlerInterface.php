<?php

namespace App\HandlerFactory;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HandlerInterface
 * @package App\HandlerFactory
 */
interface HandlerInterface
{
    /**
     * @param Request $request
     * @param mixed|null $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, $data = null, array $options = []): bool;
}
