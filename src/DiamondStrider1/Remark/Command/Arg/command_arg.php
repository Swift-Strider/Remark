<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches a string, giving the player a list
 * of commands that can be chosen from.
 *
 * @phpstan-implements Arg<string>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class command_arg implements Arg
{
    use SetParameterTrait;

    public function extract(CommandContext $context, ArgumentStack $args): string
    {
        $component = $this->toUsageComponent($this->parameter->getName());

        return $args->pop("Required argument $component");
    }

    public function toUsageComponent(string $name): ?string
    {
        return "<$name: command>";
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_COMMAND);
    }
}
