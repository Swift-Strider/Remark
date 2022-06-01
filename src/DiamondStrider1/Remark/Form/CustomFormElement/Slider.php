<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Slider implements CustomFormElement
{
    public function __construct(
        private string $text,
        private float $min,
        private float $max,
        private float $step = 1.0,
        private ?float $default = null,
    ) {
    }

    public function validate(mixed $data): void
    {
        if ((!is_float($data) && !is_int($data)) || $data < $this->min || $data > $this->max) {
            throw new FormValidationException('Invalid response to Slider element!');
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'slider',
            'text' => $this->text,
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
            'default' => $this->default ?? $this->min,
        ];
    }
}
