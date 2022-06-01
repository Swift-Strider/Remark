<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class StepSlider implements CustomFormElement
{
    /**
     * @param array<int, string> $steps
     */
    public function __construct(
        private string $text,
        private array $steps,
        private int $defaultOption = 0,
    ) {
    }

    public function validate(mixed $data): void
    {
        if (!is_int($data) && isset($this->steps[$data])) {
            throw new FormValidationException('Invalid response to StepSlider element!');
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'step_slider',
            'text' => $this->text,
            'steps' => $this->steps,
            'default' => $this->defaultOption,
        ];
    }
}
