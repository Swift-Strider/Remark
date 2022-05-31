<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Guard;

use DiamondStrider1\Remark\CommandContext;

/**
 * Protects a HandlerMethod from being called
 * when a requirement hasn't been met.
 *
 * Ex: the sender being required to have
 * permission to run a command.
 */
interface Guard
{
    /**
     * Returns whether the guard's requirements are met.
     */
    public function passes(CommandContext $context): bool;
}
