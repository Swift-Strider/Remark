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
use pocketmine\utils\Utils;
use pocketmine\utils\getNiceClassName;

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
        private ?\Closure $customExceptionHandler = null,
        public bool $explicitClose = false,
        // public bool $silentAcceptTitle = false,
        // public bool $silentAcceptBody = false,
        // public bool $silentAcceptButtons = false,
        // public bool $silentAcceptSkin = false,
    ) {
    }

    /**
     * Check if 2 buttons (instance) are equal while at the same time,
     * check if they are in the dialogue.
     *
     * @throws \InvalidArgumentException Not or no longer in the dialogue.
     */
    public function buttonEquals(
        DialogueButtonInterface $a,
        DialogueButtonInterface $b
    ) : bool {
        foreach ([$a, $b] as $target) if (!in_array($target, $this->buttons, true)) throw new \InvalidArgumentException(Utils::getNiceClassName($target) . "#" . spl_object_id($target) . " (see stacktrace) is not or is no longer in the dialogue!");

        return $a === $b;
    }

    /**
     * @param array<int, string> $buttons
     */
    public function mutateButtons(array $buttons, int $offset = 0) : self {
        if (self::get($this->player)[0] !== $this) $this->except("Mutate buttons on a closed dialogue!", "You might want to enable Dialogue::explicitClose.");

        $removed = array_splice($this->buttons, $offset, $buttons);
        // $removed = everything start at offset til the end of the original array.
        // So, things that should not be removed are added back:
        // (array_filter() to remove null or empty strings.)
        $this->buttons = array_filter([...$buttons, ...$removed[$offset + count($buttons)]]);

        // TODO: resend buttons.

        return $this;
    }

    /**
     * End this dialogue session (instance).
     */
    public function close() : void {
        if (self::get($this->player)[0] !== $this) $this->except("Close of closed dialogue!", "This is redundant if you have enabled Dialogue::explicitClose.");
        unset(self::$dialogueStore[$this->player->getName()]);

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
     * @throws DialogueException
     */
    public function then() : Thenable {
        return Thenable::promise(function ($resolve, $reject) {
            if ($this->lock) $this->except("Resend before handling response!", "Every response has to be explicitly accept() or cancel().");
            if (!$this->mutated) $this->except("Resend without mutating!", "The viewer is supposed to see the exact content without this resend.");

            if ($this->player->isOnline()) {
                if (!$this->sentBefore || $this->entity->alwaysProcess) {
                    $id = ($this->entity->entityProcessor)($this->player);
                    $this->sentBefore = true;
                }

                try {
                    $pk = NpcDialoguePacket::create(
                        $id,
                        NpcDialoguePacket::ACTION_OPEN,
                        $this->body,
                        self::SCENE_NAME,
                        $this->title,
                        json_encode(array_map(fn(
                            int $index,
                            DialogueButtonInterface $button,
                        ) : array => $button->formatAction($index), $this->buttons)),
                    );
                } catch (\JsonException $err) {
                    $this->except("json_encode(action) failed!", "Did one of the DialogueButtonInterface::formatAction() return a recursive array?", $err);
                }
                $this->player->getNetworkSession()->sendDataPacket($pk);
            } else $resolve(null);
        });
    }

    /**
     * @return \Generator<mixed, mixed, mixed, ?DialogueResponse>
     * TODO: complete doc.
     */
    public function gen() : \Generator {
        yield from Await::promise(fn($resolve, $reject) => $this->then()->then($resolve, $reject));
    }

    /**
     * Differs among plugins because of virion shading.
     */
    private const SCENE_NAME = self::class;

    /**
     * Verify if packet is correspond to a dialogue instance.
     */
    public function verify(NpcRequestPacket $pk) : bool {
        return $pk->sceneName === self::SCENE_NAME;
    }

    public static function activate(Plugin $plugin) : void {
        $pm = Server::getInstance()->getPluginManager();
        $pm->registerEvent(
            DataPacketReceiveEvent::class,
            function ($event) use ($plugin) {
                $pk = $event->getPacket();
                if(!$pk instanceof NpcRequestPacket) return;
                $event->cancel(); // Prevent console from spamming.
                $player = $event->getOrigin()->getPlayer();
                if ($player === null) {
                    $plugin->getLogger()->warning("Connection {$event->getOrigin()->getDisplayName()} might be abusing packets related to NPC dialogue!");
                    return;
                }

                [$dialogue, $resolve, $reject] = self::get($player->getName());
                if (!($dialogue?->verify($pk) ?? false)) {
                    $plugin->getLogger()->debug("Player {$player->getName()} requested unknown dialogue '$pk->sceneName'! (The server is holding " . ($dialogue === null ? "null" : Utils::getNiceClassName($dialogue) . "#" . spl_object_id($dialogue)) . ")");
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
                        $templateThat = "Player {$player->getName()} sent some NPC dialogue actions that";
                        try {
                            $buttons = json_decode($pk->commandString, true, 512, JSON_THROW_ON_ERROR);
                            foreach ($buttons as $button) {
                                $name = $button["button_name"] ?? throw new DialogueException("$templateThat have no 'button_name'!");
                                // Check for int format without leading zero:
                                if (!ctype_digit($name) || (strlen($name) > 1 && $name[0] === "0")) throw new DialogueException("$templateThat have 'button_name'='$name' and is in bad format!");
                                $index = (int)$name;
                                if (!isset($dialogue->buttons[$index])) throw new DialogueException("$templateThat have 'button_name'='$name' while the dialogue has only " . count($dialogue->buttons) . "!");
                            }
                        } catch (\JsonException $err) {
                            $reject(new DialogueException("$templateThat cannot be json_decode()!", -1, $err));
                        } catch (DialogueException $err) {
                            $reject($err);
                        }
                        if (isset($err)) return;
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
    private function except(string $err, string $help, ?\Throwable $previous = null) : void {
        throw new DialogueException("$err ($help)", -1, $previous);
    }

    /**
     * @var array<string, array{self, \Closure, \Closure}> $dialogueStore
     */
    private static $dialogueStore = [];

    /**
     * @return array{self, \Closure, ?\Closure}|array{null, null, null}
     */
    private static function get(Player $player) : ?array {
        return self::$dialogueStore[$player->getName()] ?? array_fill(0, 3, null);
    }

}
