<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Label implements CustomFormElement
{
    public function __construct(
        private string $text,
    ) {
    }

    public function validate(mixed $data): void
    {
        if (null !== $data) {
            throw new FormValidationException('Invalid response to Label element!');
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'label',
            'text' => $this->text,
        ];
    }
}
