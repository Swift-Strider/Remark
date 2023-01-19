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
     * an entity to be mirrored, and at max 6 buttons. The player
     * must either choose a single button or close the dialog.
     * The array of buttons passed to this function must be a
     * list, meaning its first entry's key is zero, the next
     * being one, etc.
     *
     * @param array<int, string> $buttons
     */
    private function __construct(
        private Player $player,
        private string $title,
        private string $content,
        private Entity $entity,
        private array $buttons,
        private bool $explicitClose = false,
    ) {
        $this->validateButtons();
    }

    private const MAX_BUTTONS = 6;

    private function validateButtons() : void {
        if ([] !== $this->buttons || $this->buttons !== array_values($this->buttons)) {
            $expected = 0;
            foreach (array_keys($this->buttons) as $index) {
                if ($index !== $expected++) {
                    throw new InvalidArgumentException('The passed array of buttons is not a list!');
                }
            }
        }
        if (count($this->buttons) > self::MAX_BUTTONS) {
            throw new InvalidArgumentException('At max ' . self::MAX_BUTTONS . ' buttons can be passed! (if you are not expecting this overflow, please check the stacktrace (backtrace) for more details.)');
        }
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
        $this->validateButtons();

        return $this;
    }

    private bool $closed;

    public function close() : self {
        // TODO: close().
        return $this;
    }

    /**
     * This function returns a Thenable that may be used when
     * `sof3/await-generator` is not present. Otherwise it's
     * recommended to use `Forms::dialog2gen()` because of
     * AwaitGenerator's simpler syntax.
     *
     * @phpstan-return Thenable<?int>
     */
    public function sendThen() : Thenable {
        if ($this->player->isOnline()) {

        }
        // TODO: sendThen().
    }

    /**
     * @return \Generator<mixed, mixed, mixed, >
     */
    public function sendGen() : \Generator {
        yield from Await::promise(fn($resolve, $reject) => $this->sendThen()->then($resolve, $reject));
    }
}
