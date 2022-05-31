<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use DiamondStrider1\Remark\CommandContext;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Parses a value to be passed as
 * the parameter to a method.
 *
 * @phpstan-template T
 */
interface Arg
{
    /**
     * Extracts the value from the given context.
     * The argument may pop from the ArgumentStack to
     * prevent the same string-arg being used by multiple `Arg`s.
     *
     * @phpstan-return T
     */
    public function extract(CommandContext $context, ArgumentStack $args): mixed;

    /**
     * Make part of a usage string, like <message: string>.
     *
     * @param string $name the name of the parameter
     *
     * @return ?string null if not applicable
     */
    public function toUsageComponent(string $name): ?string;

    /**
     * Create a CommandParameter, registering any command enums if needed.
     *
     * @param string $name the name of the parameter
     *
     * @return ?CommandParameter null if not applicable
     */
    public function toCommandParameter(string $name): ?CommandParameter;
}
