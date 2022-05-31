<?php

declare(strict_types=1);

namespace example;

use DiamondStrider1\Remark\Arg\enum;
use DiamondStrider1\Remark\Arg\remaining;
use DiamondStrider1\Remark\Arg\sender;
use DiamondStrider1\Remark\Cmd;
use DiamondStrider1\Remark\CmdConfig;
use DiamondStrider1\Remark\Guard\permission;
use pocketmine\player\Player;

#[CmdConfig(
    name: 'myplugin',
    description: "Access my plugin through subcommands",
    aliases: [],
    permissions: ['myplugin.command']
)]
final class Commands
{
    // 'myplugin' is the command, 'showoff' is the subcommand.
    // There may be as many subcommands as you want.
    #[Cmd('myplugin', 'showoff')]
    // permission is a Guard.
    #[permission('myplugin.command.showoff')]
    // There is one Arg for every parameter.
    #[sender(), enum('dance', 'dig', 'mine'), remaining()]
    public function showOff(Player $sender, string $dance, array $message): void
    {
        $message = implode(' ', $message);
        $sender->sendActionBarMessage("Dancing ($dance) - $message");
    }
}
