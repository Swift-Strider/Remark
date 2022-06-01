<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use pocketmine\command\CommandSender;

/**
 * Represents an instance of a command sender
 * running a command.
 */
final class CommandContext
{
    /**
     * @param string[] $args
     */
    public function __construct(
        private CommandSender $sender,
        private array $args,
    ) {
    }

    public function sender(): CommandSender
    {
        return $this->sender;
    }

    /**
     * @return string[]
     */
    public function args(): array
    {
        return $this->args;
    }
}
