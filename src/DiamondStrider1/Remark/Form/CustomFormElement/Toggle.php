<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Toggle implements CustomFormElement
{
    public function __construct(
        private string $text,
        private bool $default = false,
    ) {
    }

    public function validate(mixed $data): void
    {
        if (!is_bool($data)) {
            throw new FormValidationException('Invalid response to Toggle element!');
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'toggle',
            'text' => $this->text,
            'default' => $this->default,
        ];
    }
}
