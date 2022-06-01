<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form;

use Closure;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * An internally used modal form implementation.
 * Plugins should instead use `Forms::modal2gen()`
 * or `Forms::modal2then()`.
 */
final class InternalModalForm implements Form
{
    public function __construct(
        private Closure $resolve,
        private Closure $reject,
        private string $title,
        private string $content,
        private string $yesText,
        private string $noText,
    ) {
    }

    public function handleResponse(Player $player, $data): void
    {
        if (!is_bool($data)) {
            $exception = new FormValidationException('Expected a response of type bool, got type '.gettype($data).' instead!');
            ($this->reject)($exception);
            throw $exception;
        }

        ($this->resolve)($data);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'modal',
            'title' => $this->title,
            'content' => $this->content,
            'button1' => $this->yesText,
            'button2' => $this->noText,
        ];
    }
}
