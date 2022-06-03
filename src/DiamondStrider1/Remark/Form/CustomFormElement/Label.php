<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

/**
 * @phpstan-implements CustomFormElement<null>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Label implements CustomFormElement
{
    public function __construct(
        private string $text,
    ) {
    }

    public function extract(mixed $data): mixed
    {
        if (null !== $data) {
            throw new FormValidationException('Invalid response to Label element!');
        }

        return null;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'label',
            'text' => $this->text,
        ];
    }
}
