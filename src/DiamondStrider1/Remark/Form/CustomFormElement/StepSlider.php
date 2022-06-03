<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use Attribute;
use pocketmine\form\FormValidationException;

/**
 * @phpstan-implements CustomFormElement<int>
 */
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

    public function extract(mixed $data): int
    {
        if (!is_int($data) || !isset($this->steps[$data])) {
            throw new FormValidationException('Invalid response to StepSlider element!');
        }

        return $data;
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
