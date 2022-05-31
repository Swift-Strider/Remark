<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\Packet;
use pocketmine\Server;

final class CommandHintListener implements Listener
{
    /** @var Packet[] ignored to prevent recursion */
    private array $ignored = [];

    public function onDataPacketSend(DataPacketSendEvent $ev): void
    {
        foreach ($ev->getPackets() as $pk) {
            if (!$pk instanceof AvailableCommandsPacket) {
                continue;
            }
            if (in_array($pk, $this->ignored, true)) {
                continue;
            }

            $oldTargets = $ev->getTargets();
            $newTargets = $oldTargets;
            foreach ($oldTargets as $index => $target) {
                $clonedPk = clone $pk;
                if ($this->handleACP($clonedPk, $target)) {
                    $this->ignored[] = $clonedPk;
                    $target->sendDataPacket($clonedPk);
                    unset($newTargets[$index]);
                    $ev->cancel();
                }
            }

            if ($ev->isCancelled()) {
                $this->ignored[] = $pk;
                foreach ($newTargets as $target) {
                    $target->sendDataPacket($pk);
                }
            }
            $this->ignored = [];
        }
    }

    /**
     * @return bool whether the packet was modified and should be resent
     */
    private function handleACP(AvailableCommandsPacket $pk, NetworkSession $target): bool
    {
        $player = $target->getPlayer();
        if (null === $player) {
            return false;
        }

        foreach (array_keys($pk->commandData) as $name) {
            $command = Server::getInstance()->getCommandMap()->getCommand($name);
            if (!$command instanceof BoundCommand) {
                continue;
            }
            if (!$command->checkVisibility($player)) {
                continue;
            }

            $pk->commandData[$name]->overloads = $command->getOverloads();
        }

        return true;
    }
}
