<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use Attribute;
use DiamondStrider1\Remark\CommandContext;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\player\Player as PmPlayer;
use pocketmine\Server;

/**
 * Matches an online player from the server.
 * Optionally requires the given string to
 * exactly match a player's name.
 *
 * @phpstan-implements Arg<PmPlayer>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_METHOD
)]
final class player_arg implements Arg
{
    /**
     * @param bool $exact whether to not match by prefix
     */
    public function __construct(
        private bool $exact = false,
    ) {
    }

    public function extract(CommandContext $context, ArgumentStack $args): PmPlayer
    {
        if ($this->exact) {
            $player = Server::getInstance()->getPlayerExact($args->pop());
        } else {
            $player = Server::getInstance()->getPlayerByPrefix($args->pop());
        }

        return $player ?? throw new ExtractionFailed();
    }

    public function toUsageComponent(string $name): ?string
    {
        return "<$name: target>";
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_TARGET);
    }
}
