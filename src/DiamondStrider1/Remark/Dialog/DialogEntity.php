<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialog;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\player\Player;

/**
 * Contains an {@link self::entityProcessor} which will be
 * called before sending the dialog.
 */
class DialogEntity
{
    /**
     * False = process entity only when the dialog is sent first.
     */
    public bool $alwaysProcess = false;

    /**
     * @param \Closure(Player): int $entityProcessor Returns the entity runtime ID.
     */
    private function __construct(
        public \Closure $entityProcessor
    ) {
    }

    /**
     * Determin the suitable entity processor.
     * {@link self::offset()} is for all entities with a network
     * type ID of {@link EntityIds::PLAYER}.
     * For any others, {@link self::normal()} will be chosen.
     *
     * If you wish to send a dialog without an entity, you may use {@link self::fake()}.
     */
    public static function auto(Entity $entity) : self {
        return $entity->getNetworkTypeId() === EntityIds::PLAYER
        ? self::offset($entity)
        : self::normal($entity);
    }

    /**
     * @author Tobias Grether ({@link https://github.com/TobiasGrether})
     */
    private const PICKER_OFFSET = -50;

    /**
     * Regarding entities with the network type ID of
     * {@link EntityIds::PLAYER} (players / NPCs / actors with
     * custom geomtry), there is a client-side rendering problem
     * that their body sink into the lower half of a dialog.
     *
     * This is why such a workaround exists:
     */
    public static function offset(Entity $entity) : self {
        static $skinIndex = json_encode([
            "picker_offsets" => [
                "scale" => [0, 0, 0],
                "translate" => [0, 0, 0],
            ],
            "portrait_offsets" => [
                "scale" => [1, 1, 1],
                "translate" => [0, self::PICKER_OFFSET, 0]
            ]
        ]);

        return new self(function ($player, $skinIndex) {
            // Inheritance seems more suitable here, but sorry,
            // @Endermanbugzjfc is way more lazier than you thought.
            (self::normal($entity)->entityProcessor)($player);
            $prop = $entity->getNetworkProperties();
            $prop->setString(EntityMetadataProperties::NPC_SKIN_INDEX, $skinIndex);

            return $entity->getId();
        });
    }

    /**
     * Simply sets necessary network properties.
     */
    public static function normal(Entity $entity) : self {
        return new self(function ($player) use ($entity) {
            $prop = $entity->getNetworkProperties();
            $prop->setByte(EntityMetadataProperties::HAS_NPC_COMPONENT, 1);
            $prop->setString(EntityMetadataProperties::INTERACTIVE_TAG, $this->dialogueBody);
            $prop->setString(EntityMetadataProperties::NPC_ACTIONS, $mappedActions);

            return $entity->getId();
        });
    }

    /**
     * Dialog without an entity.
     *
     * Digression:
     * A menu (simple) form would be better in this case, where
     * you can have more than 6 buttons.
     * Unless you wish to have more control, such as mutating the
     * content or force-closing before the viewer responses.
     */
    public static function fake() : self {
        return new self(function ($player) {
            $id = Entity::nextRuntimeId();
            $player->getNetworkSession()->sendDataPacket(
                AddActorPacket::create(
                    $id,
                    $id,
                    $player->getPosition()->add(0, 10, 0),
                    null,
                    $player->getLocation()->getPitch(),
                    $player->getLocation()->getYaw(),
                    $player->getLocation()->getYaw(),
                    [],
                    [
                        EntityMetadataProperties::HAS_NPC_COMPONENT => new ByteMetadataProperty(1),
                        EntityMetadataProperties::INTERACTIVE_TAG => new StringMetadataProperty($this->dialogueBody),
                        EntityMetadataProperties::NPC_ACTIONS => new StringMetadataProperty($mappedActions),
                        // EntityMetadataProperties::VARIANT => new IntMetadataProperty(0), // Variant affects NPC skin
                    ],
                    []
                )
            );

            return $id;
        });
    }
}
