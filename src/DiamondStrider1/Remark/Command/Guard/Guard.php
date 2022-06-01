<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Guard;

use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\lang\Translatable;

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
     * @return string|Translatable|null null if the requirement is met or
     *                                  a string/Translatable to send to
     *                                  the player
     */
    public function passes(CommandContext $context): null|string|Translatable;
}
