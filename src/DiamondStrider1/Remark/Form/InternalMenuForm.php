<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form;

use Closure;
use DiamondStrider1\Remark\Form\MenuFormElement\MenuFormButton;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * An internally used menu form implementation.
 * Plugins should instead use `Forms::menu2gen()`
 * or `Forms::menu2then()`.
 */
final class InternalMenuForm implements Form
{
    /**
     * @param array<int, MenuFormButton> $buttons
     */
    public function __construct(
        private Closure $resolve,
        private Closure $reject,
        private string $title,
        private string $content,
        private array $buttons,
    ) {
    }

    public function handleResponse(Player $player, $data): void
    {
        if (
            null !== $data &&
            (!is_int($data) || $data >= count($this->buttons))
        ) {
            $exception = new FormValidationException('Expected a response of type null|int, got type '.gettype($data).' instead!');
            ($this->reject)($exception);
            throw $exception;
        }

        ($this->resolve)($data);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'form',
            'title' => $this->title,
            'content' => $this->content,
            'buttons' => $this->buttons,
        ];
    }
}
