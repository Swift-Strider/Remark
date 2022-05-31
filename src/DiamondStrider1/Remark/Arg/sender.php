<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use Attribute;
use DiamondStrider1\Remark\CommandContext;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\player\Player;

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
    /**
     * @param bool $player require the sender to be a player
     */
    public function __construct(
        private bool $player = false,
    ) {
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
