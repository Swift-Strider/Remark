<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialog;

use DiamondStrider1\Remark\Async\Thenable;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class Dialog
{
    /**
     * Creates an NPC dialog that consists of a title, content,
     * an entity to be mirrored, and at max 6 buttons (generally).
     * The player must either choose a single button or close the
     * dialog.
     * The array of buttons passed to this function must be a
     * list, meaning its first entry's key is zero, the next
     * being one, etc.
     */
    private function __construct(
        private Player $player,
        private string $title,
        private string $content,
        private DialogEntity $entity,
        private DialogButtonMap $buttons,
        private bool $explicitClose = false,
    ) {
    }

    /**
     * @param array<int, string> $buttons
     */
    public function mutateButtons(array $buttons, int $offset = 0) : self {
        if ($this->closed) throw new DialogException("Cannot mutate buttons on a closed dialog!" . ($this->explicitClose ? " (You might want to enable Dialog::explicitClose.)" : ""));

        $removed = array_splice($this->buttons, $offset, $buttons);
        // $removed = everything start at offset til the end of the original array.
        // So, things that should not be removed are added back:
        // (array_filter() to remove null or empty strings.)
        $this->buttons = array_filter([...$buttons, ...$removed[$offset + count($buttons)]]);

        // TODO: resend buttons.

        return $this;
    }

    private bool $closed;

    public function close() : self {
        // TODO: close().
        return $this;
    }

    private bool $sentBefore = false;

    /**
     * This function returns a Thenable that may be used when
     * `sof3/await-generator` is not present. Otherwise it's
     * recommended to use `Forms::dialog2gen()` because of
     * AwaitGenerator's simpler syntax.
     *
     * Notice that unlike forms, dialogs can be resent before
     * a player response to it. In such case, a null will take
     * place of a DialogResponse.
     *
     * @phpstan-return Thenable<?DialogResponse>
     */
    public function then() : Thenable {
        if ($this->player->isOnline()) {
            if (!$this->sentBefore || $this->dialogEntity->alwaysProcess) {
                ($this->dialogEntity->entityProcessor)($this->player);
                $this->sentBefore = true;
            }
        }
    }

    /**
     * @return \Generator<mixed, mixed, mixed, ?DialogResponse>
     * TODO: complete doc.
     */
    public function gen() : \Generator {
        yield from Await::promise(fn($resolve, $reject) => $this->sendThen()->then($resolve, $reject));
    }
}
