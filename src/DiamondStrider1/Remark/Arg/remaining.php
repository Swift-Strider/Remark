<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use Attribute;
use DiamondStrider1\Remark\CommandContext;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches the remaining arguments.
 *
 * @phpstan-implements Arg<string[]>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_METHOD
)]
final class remaining implements Arg
{
    use SetParameterTrait;

    /**
     * @return string[]
     */
    public function extract(CommandContext $context, ArgumentStack $args): array
    {
        $collected = [];
        while (true) {
            $popped = $args->tryPop();
            if (null === $popped) {
                break;
            }
            $collected[] = $popped;
        }

        return $collected;
    }

    public function toUsageComponent(string $name): ?string
    {
        return "<$name: text>";
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_RAWTEXT);
    }
}
