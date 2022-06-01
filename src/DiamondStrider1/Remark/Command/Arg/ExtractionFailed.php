<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Exception;
use pocketmine\lang\Translatable;

final class ExtractionFailed extends Exception
{
    public function __construct(private string|Translatable $translatable)
    {
        parent::__construct(is_string($translatable) ? $translatable : $translatable->getText());
    }

    public function getTranslatable(): string|Translatable
    {
        return $this->translatable;
    }
}
