<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

/**
 * @phpstan-implements CustomFormElement<int>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Dropdown implements CustomFormElement
{
    /**
     * @param array<int, string> $options
     * @param bool               $allowDefault whether the player may skip filling
     *                                         out a dropdown when its default
     *                                         value is set out of range (ex: -1)
     */
    public function __construct(
        private string $text,
        private array $options,
        private int $defaultOption = 0,
        private bool $allowDefault = true,
    ) {
    }

    public function extract(mixed $data): int
    {
        if (
            (!is_int($data) || !isset($this->options[$data])) &&
            !($data === $this->defaultOption && $this->allowDefault)
        ) {
            throw new FormValidationException('Invalid response to Dropdown element!');
        }

        return $data;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'dropdown',
            'text' => $this->text,
            'options' => $this->options,
            'default' => $this->defaultOption,
        ];
    }
}
