<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

final class CommandHintListener implements Listener
{
    /**
     * @priority HIGH
     */
    public function onDataPacketSend(DataPacketSendEvent $ev): void
    {
        foreach ($ev->getPackets() as $pk) {
            if (!$pk instanceof AvailableCommandsPacket) {
                continue;
            }

            foreach (array_keys($pk->commandData) as $name) {
                $command = Server::getInstance()->getCommandMap()->getCommand($name);
                if (!$command instanceof BoundCommand) {
                    continue;
                }

                $pk->commandData[$name]->overloads = $command->getOverloads();
            }
        }
    }
}
