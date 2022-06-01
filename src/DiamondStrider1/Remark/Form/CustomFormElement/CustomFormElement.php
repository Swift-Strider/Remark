<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use JsonSerializable;
use pocketmine\form\FormValidationException;

interface CustomFormElement extends JsonSerializable
{
    /**
     * @throws FormValidationException
     */
    public function validate(mixed $data): void;
}
