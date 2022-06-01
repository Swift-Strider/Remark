<?php

declare(strict_types=1);

namespace example;

use DiamondStrider1\Remark\Command\Arg\enum;
use DiamondStrider1\Remark\Command\Arg\remaining;
use DiamondStrider1\Remark\Command\Arg\sender;
use DiamondStrider1\Remark\Command\Cmd;
use DiamondStrider1\Remark\Command\CmdConfig;
use DiamondStrider1\Remark\Command\Guard\permission;
use DiamondStrider1\Remark\Form\Forms;
use DiamondStrider1\Remark\Form\MenuFormElement\MenuFormButton;
use DiamondStrider1\Remark\Form\MenuFormElement\MenuFormImage;
use Generator;
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
    public function showOff(Player $sender, string $dance, array $message): Generator
    {
        $message = implode(' ', $message);
        $choice2response = ['bland', 'amazing', 'AWESOME'];
        $photoDirt = 'textures/blocks/dirt.png';
        $photoGold = 'textures/blocks/gold_block.png';
        $photoAwesome = 'https://unsplash.com/photos/3k9PGKWt7ik/download?ixid=MnwxMjA3fDB8MXxhbGx8fHx8fHx8fHwxNjU0MDg0MTAy&force=true&w=640';
        $choice = yield from Forms::menu2gen(
            $sender,
            'How do you like this dance?',
            "The message you sent will be logged:\n$message",
            [
                new MenuFormButton('bland', new MenuFormImage('path', $photoDirt)),
                new MenuFormButton('amazing', new MenuFormImage('path', $photoGold)),
                new MenuFormButton('awesome', new MenuFormImage('url', $photoAwesome)),
            ]
        );
        $choice = $choice !== null ? $choice2response[$choice] : "Very Undecidable";
        $sender->sendMessage("You found the dance §g$choice!");
    }
}
