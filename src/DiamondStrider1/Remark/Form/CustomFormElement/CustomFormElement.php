<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\CustomFormElement;

use JsonSerializable;
use pocketmine\form\FormValidationException;

/**
 * @phpstan-template ExtractionType
 */
interface CustomFormElement extends JsonSerializable
{
    /**
     * @throws FormValidationException
     * @phpstan-return ExtractionType
     */
    public function extract(mixed $data): mixed;
}
