<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

/**
 * @phpstan-implements CustomFormElement<string>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Input implements CustomFormElement
{
    public function __construct(
        private string $text,
        private string $placeholder = '',
        private string $default = '',
    ) {
    }

    public function extract(mixed $data): string
    {
        if (!is_string($data)) {
            throw new FormValidationException('Invalid response to Input element!');
        }

        return $data;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'input',
            'text' => $this->text,
            'placeholder' => $this->placeholder,
            'default' => $this->default,
        ];
    }
}
