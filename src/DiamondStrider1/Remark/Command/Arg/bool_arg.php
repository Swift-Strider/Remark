<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches a true / false boolean.
 *
 * Valid command arguments for both choices are:
 * - true: "true", "on", and "yes"
 * - false: "false", "off", and "no"
 *
 * @phpstan-implements Arg<bool|null>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class bool_arg implements Arg
{
    use SetParameterTrait;

    public function extract(CommandContext $context, ArgumentStack $args): ?bool
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

        return match ($value) {
            'true', 'on', 'yes' => true,
            'false', 'off', 'no' => false,
            default => throw new ExtractionFailed("$value does not satisfy $component"),
        };
    }

    public function toUsageComponent(string $name): ?string
    {
        if ($this->optional) {
            return "[$name: true|false]";
        } else {
            return "<$name: true|false>";
        }
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::enum($name, new CommandEnum('bool', ['true', 'false']), 0, $this->optional);
    }
}
