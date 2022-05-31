<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use Attribute;
use DiamondStrider1\Remark\CommandContext;
use InvalidArgumentException;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches a fixed number of strings or the
 * remainder of strings passed.
 *
 * If count is set to `1` the parameter type
 * must be `string`. For any other setting the
 * parameter type must be `string[]`
 *
 * @phpstan-implements Arg<string|string[]>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_METHOD
)]
final class raw implements Arg
{
    use SetParameterTrait;

    /**
     * @param ?int $count when null matches the remaining arguments
     */
    public function __construct(
        private ?int $count = 1,
    ) {
        if (null !== $count && $count <= 0) {
            throw new InvalidArgumentException('Count must be null or greater than zero!');
        }
    }

    /**
     * @return string|string[]
     */
    public function extract(CommandContext $context, ArgumentStack $args): string|array
    {
        if (1 === $this->count) {
            return $args->pop();
        }

        if (null === $this->count) {
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

        $collected = [];
        while (count($collected) < $this->count) {
            $collected[] = $args->pop();
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
