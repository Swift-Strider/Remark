<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

final class CommandHintListener implements Listener
{
    public function onDataPacketSend(DataPacketSendEvent $ev): void
    {
        $targets = $ev->getTargets();
        foreach ($ev->getPackets() as $pk) {
            if (!$pk instanceof AvailableCommandsPacket) {
                continue;
            }

            foreach (array_keys($pk->commandData) as $name) {
                $command = Server::getInstance()->getCommandMap()->getCommand($name);
                if (!$command instanceof BoundCommand) {
                    continue;
                }

                $invisible = true;
                foreach ($targets as $target) {
                    $player = $target->getPlayer();
                    if (null === $player || $command->checkVisibility($player)) {
                        $invisible = false;
                        continue;
                    }
                }
                if ($invisible) {
                    continue;
                }

                $pk->commandData[$name]->overloads = $command->getOverloads();
            }
        }
    }
}
