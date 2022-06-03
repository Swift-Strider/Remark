<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

/**
 * @phpstan-implements CustomFormElement<bool>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Toggle implements CustomFormElement
{
    public function __construct(
        private string $text,
        private bool $default = false,
    ) {
    }

    public function extract(mixed $data): bool
    {
        if (!is_bool($data)) {
            throw new FormValidationException('Invalid response to Toggle element!');
        }

        return $data;
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
