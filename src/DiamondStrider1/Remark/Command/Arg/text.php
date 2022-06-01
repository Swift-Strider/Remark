<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use InvalidArgumentException;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Matches a fixed number of arguments.
 *
 * @phpstan-implements Arg<null|string|string[]>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class text implements Arg
{
    use SetParameterTrait;

    /**
     * @param int  $count   number of arguments to match
     * @param bool $require whether to fail when less
     *                      than $count arguments remain
     */
    public function __construct(
        private int $count = 1,
        private bool $require = true,
    ) {
        if ($count <= 0) {
            throw new InvalidArgumentException('Count must greater than zero!');
        }
    }

    public function setParameter(ReflectionParameter $parameter): void
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            if (
                1 === $this->count && $this->require &&
                ('string' !== $type->getName() || $type->allowsNull())
            ) {
                $name = $parameter->getName();
                throw new InvalidArgumentException("The corresponding parameter ($name) to `text()` must have the type `string` (hint: `?string` is invalid)!");
            } elseif (
                1 === $this->count && !$this->require &&
                ('string' !== $type->getName() || !$type->allowsNull())
            ) {
                $name = $parameter->getName();
                throw new InvalidArgumentException("The corresponding parameter ($name) to `text()` must have the type `?string` (hint: `string` is invalid)!");
            } elseif (
                1 !== $this->count &&
                ('array' !== $type->getName() || $type->allowsNull())
            ) {
                $name = $parameter->getName();
                throw new InvalidArgumentException("The corresponding parameter ($name) to `text()` must have the type `array` (hint: `?array` is invalid)!");
            }
        }
        $this->parameter = $parameter;
    }

    /**
     * @return string|string[]|null
     */
    public function extract(CommandContext $context, ArgumentStack $args): null|string|array
    {
        if (1 === $this->count && $this->require) {
            $component = $this->toUsageComponent($this->parameter->getName());

            return $args->pop("Required argument $component");
        } elseif (1 === $this->count) {
            return $args->tryPop();
        }

        if ($this->require) {
            $collected = [];
            while (count($collected) < $this->count) {
                $component = $this->toUsageComponent($this->parameter->getName());
                $collected[] = $args->pop("$component is not satisfied");
            }

            return $collected;
        } else {
            $collected = [];
            while (count($collected) < $this->count) {
                $popped = $args->tryPop();
                if (null === $popped) {
                    break;
                }
                $collected[] = $popped;
            }

            return $collected;
        }
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
