<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\player\Player as PmPlayer;
use pocketmine\Server;

/**
 * Matches an online player from the server.
 * Optionally requires the given string to
 * exactly match a player's name.
 *
 * @phpstan-implements Arg<PmPlayer|null>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class player_arg implements Arg
{
    use SetParameterTrait;

    /**
     * @param bool $exact whether to not match by prefix
     */
    public function __construct(
        private bool $exact = false,
    ) {
    }

    public function extract(CommandContext $context, ArgumentStack $args): ?PmPlayer
    {
        $component = $this->toUsageComponent($this->parameter->getName());
        if ($this->optional) {
            $name = $args->tryPop();
            if (null === $name) {
                return null;
            }
        } else {
            $name = $args->pop("Required argument $component");
        }
        if ($this->exact) {
            $player = Server::getInstance()->getPlayerExact($name);
        } else {
            $player = Server::getInstance()->getPlayerByPrefix($name);
        }

        return $player ?? throw new ExtractionFailed(KnownTranslationFactory::commands_generic_player_notFound());
    }

    public function toUsageComponent(string $name): ?string
    {
        if ($this->optional) {
            return "[$name: target]";
        } else {
            return "<$name: target>";
        }
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_TARGET, 0, $this->optional);
    }
}
