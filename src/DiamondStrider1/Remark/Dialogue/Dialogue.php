<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

use DiamondStrider1\Remark\Async\Thenable;
use SOFe\AwaitGenerator\Await;
use pocketmine\Server;
use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\NpcDialoguePacket;
use pocketmine\network\mcpe\protocol\NpcRequestPacket;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

/**
 * "Change" refers to client-side requests of changing content.
 * "Mutate" refers to plugin operations of changing content.
 * A dialogue instance represent ONLY one dialogue session, which ends
 * when it closes.
 */
class Dialogue {
    /**
     * Creates an NPC dialogue in which its content consists of a
     * title, body (text), an entity to be mirrored, and at max
     * 6 buttons (generally).
     * The player must either choose a single button or close the
     * dialogue.
     * The array of buttons passed to this function must be a
     * list, meaning its first entry's key is zero, the next
     * being one, etc.
     *
     * @param DialogueButton[] $buttons
     */
    private function __construct(
        private Player $player,
        private string $title,
        private string $body,
        private DialogueEntity $entity,
        private array $buttons,
        public bool $explicitClose = false,
        // public bool $silentAcceptTitle = false,
        // public bool $silentAcceptBody = false,
        // public bool $silentAcceptButtons = false,
        // public bool $silentAcceptSkin = false,
    ) {
    }

    /**
     * @param array<int, string> $buttons
     */
    public function mutateButtons(array $buttons, int $offset = 0) : self {
        if ($this->closed) $this->except("Mutate buttons on a closed dialogue!", "You might want to enable Dialogue::explicitClose.");

        $removed = array_splice($this->buttons, $offset, $buttons);
        // $removed = everything start at offset til the end of the original array.
        // So, things that should not be removed are added back:
        // (array_filter() to remove null or empty strings.)
        $this->buttons = array_filter([...$buttons, ...$removed[$offset + count($buttons)]]);

        // TODO: resend buttons.

        return $this;
    }

    private bool $closed;

    /**
     * End this dialogue session (instance).
     */
    public function close() : void {
        if ($this->closed) {
            $this->except("Close of closed dialogue!", "This is redundant if you have enabled Dialogue::explicitClose.");
        }
        if ($this->player->isOnline()) {
            ($this->entity->entityProcessor)($this->player);
        }
        // TODO: More close logic.
    }

    /**
     * True = dialogue has already been sent first.
     * This field exists to control the calling of
     * {@link DialogueEntity::entityProcessor}.
     */
    private bool $sentBefore = false;

    /**
     * Initial mutate = construction of this dialogue instance.
     * (Opens this dialogue session).
     */
    private bool $mutated = true;

    /**
     * This function returns a Thenable that may be used when
     * `sof3/await-generator` is not present. Otherwise it's
     * recommended to use `Forms::dialogue2gen()` because of
     * AwaitGenerator's simpler syntax.
     *
     * Notice that unlike forms, dialogues can be resent before
     * a player response to it. In such case, a null will take
     * place of a DialogueResponse.
     *
     * @phpstan-return Thenable<?DialogueResponse>
     */
    public function then() : Thenable {
        if ($this->lock) $this->except("Resend before handling response!", "Every response has to be explicitly accept() or cancel().");
        if (!$this->mutated) $this->except("Resend without mutating!", "The viewer is supposed to see the exact content without this resend.");

        if ($this->player->isOnline()) {
            if (!$this->sentBefore || $this->entity->alwaysProcess) {
                $id = ($this->entity->entityProcessor)($this->player);
                $this->sentBefore = true;
            }

            $pk = NpcDialoguePacket::create(
                $id,
                NpcDialoguePacket::ACTION_OPEN,
                $this->body,
                "",
                $this->title,
                $mappedActions // TODO: mapped actions.
            );
            $this->player->getNetworkSession()->sendDataPacket($pk);
        }

        // TODO: handle offline.
    }

    /**
     * @return \Generator<mixed, mixed, mixed, ?DialogueResponse>
     * TODO: complete doc.
     */
    public function gen() : \Generator {
        yield from Await::promise(fn($resolve, $reject) => $this->then()->then($resolve, $reject));
    }

    /**
     * Verify if packet is correspond to a dialogue instance.
     */
    public function verify(NpcRequestPacket $pk) : bool {
        // TODO: verify().
    }

    public static function activate(Plugin $plugin) : void {
        $pm = Server::getInstance()->getPluginManager();
        $pm->registerEvent(
            DataPacketReceiveEvent::class,
            function ($event) use ($plugin) {
                $pk = $event->getPacket();
                if($pk instanceof NpcRequestPacket){
                    $event->cancel(); // Prevent console from spamming.
                    $player = $event->getOrigin()->getPlayer();
                    if ($player === null) {
                        $plugin->getLogger()->warning("Connection {$event->getOrigin()->getDisplayName()} might be abusing packets related to NPC dialogue!");
                        return;
                    }

                    [$dialogue, $resolve, $reject] = self::$dialogueUeueue[$player->getName()] ?? null;
                    if (!($dialogue?->verify($pk) ?? false)) {
                        return;
                    }
                    $unlocker = fn() => $dialogue->lock = false;

                    $respArgs = [$dialogue, $unlocker];
                    $resp = null;
                    switch ($pk->requestType) {
                        // REQUEST_SET_ACTIONS is received when the
                        // player modified the content of button.
                        // but we need more debugging to know the all types of actions
                        // for now, we just handle the button changed
                        case NpcRequestPacket::REQUEST_SET_ACTIONS:
                        $resp = new DialogueButtonChangeResponse(...$respArgs);
                        try {
                            $resp->buttons = DialogueButton::jsonUnserialize($packet->commandString);
                        } catch (\JsonException $err) {
                                $reject($err); // TODO: sec vul.
                                return;
                            }
                            break;

                        // REQUEST_EXECUTE_ACTION is received when player pressed the button
                        // Endermanbugzjfc: I use press instead of click because the word was also used in other places of this lib.
                            case NpcRequestPacket::REQUEST_EXECUTE_ACTION:
                            $resp = new DialogueButtonPressResponse(...$respArgs);
                            $button = $dialogue->buttons[$packet->actionIndex] ?? null;
                            if ($button === null) {
                                $reject(new DialogueException("Button index overflow!"));
                                return;
                            }
                            break;

                        // REQUEST_SET_NAME is received when player tried to change the dialogue's name
                        /*
                        $newName = $packet->commandString;
                        $npcDialogue->onSetNameRequested($newName);
                        */
                        // TODO: Need debug
                        case NpcRequestPacket::REQUEST_SET_NAME:
                        break;

                        // REQUEST_SET_INTERACTION_TEXT is received when player tried to modify the dialogue body
                        // TODO
                        case NpcRequestPacket::REQUEST_SET_INTERACTION_TEXT:
                        break;

                        // REQUEST_SET_SKIN is received when player tried to change the skin of NPC
                        // Currently we don't know what integer should be sent to change the NPC skin
                        // TODO: we need to find out the types of skin
                        /** @link NpcDialogue::sendTo() */
                        case NpcRequestPacket::REQUEST_SET_SKIN:
                        break;
                    }

                    $resolve($resp);
                }
            },
            EventPriority::NORMAL, // cancel() is used to prevent console from spamming.
            $plugin
        );
    }

    /**
     * Locks {@link $this->gen()} / {@link $this->then()} when receives response.
     */
    private bool $lock = false;

    /**
     * @throws DialogueException
     */
    private function except(string $err, string $help) : void {
        throw new DialogueException("$err ($help)");
    }
}
