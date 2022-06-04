<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches an integer.
 *
 * @phpstan-implements Arg<int|null>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class int_arg implements Arg
{
    use SetParameterTrait;

    public function extract(CommandContext $context, ArgumentStack $args): ?int
    {
        $component = $this->toUsageComponent($this->parameter->getName());
        if ($this->optional) {
            $value = $args->tryPop();
            if (null === $value) {
                return null;
            }
        } else {
            $value = $args->pop("Required argument $component");
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        throw new ExtractionFailed("$value does not satisfy $component");
    }

    public function toUsageComponent(string $name): ?string
    {
        if ($this->optional) {
            return "[$name: int]";
        } else {
            return "<$name: int>";
        }
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_INT, 0, $this->optional);
    }
}
