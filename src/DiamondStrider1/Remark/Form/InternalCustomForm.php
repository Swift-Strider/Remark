<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form;

use Closure;
use DiamondStrider1\Remark\Form\CustomFormElement\Dropdown;
use DiamondStrider1\Remark\Form\CustomFormElement\Input;
use DiamondStrider1\Remark\Form\CustomFormElement\Label;
use DiamondStrider1\Remark\Form\CustomFormElement\Slider;
use DiamondStrider1\Remark\Form\CustomFormElement\StepSlider;
use DiamondStrider1\Remark\Form\CustomFormElement\Toggle;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * An internally used custom form implementation.
 * Plugins should instead create a class that uses
 * the CustomFormResultTrait or for more fine-grained
 * control use `Forms::custom2gen()` or `Forms::custom2then()`.
 */
final class InternalCustomForm implements Form
{
    /**
     * @param array<int, Dropdown|Input|Label|Slider|StepSlider|Toggle> $elements
     */
    public function __construct(
        private Closure $resolve,
        private Closure $reject,
        private string $title,
        private array $elements,
    ) {
    }

    public function handleResponse(Player $player, $data): void
    {
        if (null === $data) {
            ($this->resolve)(null);
        }

        if (!is_array($data)) {
            $exception = new FormValidationException('Expected a response of type null|array, got type '.gettype($data).' instead!');
            ($this->reject)($exception);
            throw $exception;
        }

        $extracted = [];
        try {
            foreach ($this->elements as $index => $element) {
                if (!array_key_exists($index, $data)) {
                    throw new FormValidationException("Expected response to have a value at index {$index}, but nothing was given!");
                }
                $extracted[$index] = $element->extract($data[$index]);
            }
        } catch (FormValidationException $e) {
            ($this->reject)($e);
            throw $e;
        }

        ($this->resolve)($extracted);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'custom_form',
            'title' => $this->title,
            'content' => $this->elements,
        ];
    }
}
