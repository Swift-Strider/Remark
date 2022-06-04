<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches a string WITHOUT validating that
 * it's proper JSON.
 *
 * @phpstan-implements Arg<string|null>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class json_arg implements Arg
{
    use SetParameterTrait;

    public function extract(CommandContext $context, ArgumentStack $args): ?string
    {
        if ($this->optional) {
            return $args->tryPop();
        } else {
            $component = $this->toUsageComponent($this->parameter->getName());

            return $args->pop("Required argument $component");
        }
    }

    public function toUsageComponent(string $name): ?string
    {
        if ($this->optional) {
            return "[$name: json]";
        } else {
            return "<$name: json>";
        }
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_JSON, 0, $this->optional);
    }
}
