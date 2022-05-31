<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use Attribute;
use DiamondStrider1\Remark\CommandContext;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\player\Player;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Matches the sender and optionally fails if the sender is
 * not a player.
 *
 * @phpstan-implements Arg<CommandSender>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_METHOD
)]
final class sender implements Arg
{
    use SetParameterTrait;
    private bool $player;

    public function setParameter(ReflectionParameter $parameter): void
    {
        $type = $parameter->getType();
        if (
            !$type instanceof ReflectionNamedType ||
            (
                CommandSender::class !== $type->getName() &&
                Player::class !== $type->getName()
            )
        ) {
            $name = $parameter->getName();
            throw new InvalidArgumentException("The parameter ($name) corresponding to `sender()` Arg must have a type of either CommandSender or Player!");
        }

        $this->player = Player::class === $type->getName();
        $this->parameter = $parameter;
    }

    public function extract(CommandContext $context, ArgumentStack $args): mixed
    {
        $sender = $context->sender();
        if ($this->player && !$sender instanceof Player) {
            throw new ExtractionFailed();
        }

        return $sender;
    }

    public function toUsageComponent(string $name): ?string
    {
        return null;
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return null;
    }
}
