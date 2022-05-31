<?php

declare(strict_types=1);

namespace example;

use DiamondStrider1\Remark\Remark;
use pocketmine\plugin\PluginBase;

final class Plugin extends PluginBase
{
    public function onEnable(): void
    {
        // activate() and bind() should only be called once.

        Remark::activate($this); // Allows type hints to appear in-game.
        Remark::bind($this, new Commands()); // Registers the commands to the server.
    }
}
