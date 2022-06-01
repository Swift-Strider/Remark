<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use Attribute;

/**
 * Marks a method as a handler for a command.
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class Cmd
{
    /** @var string[] */
    private array $subNames;

    public function __construct(
        private string $name,
        string ...$subNames,
    ) {
        $this->subNames = $subNames;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function subNames(): array
    {
        return $this->subNames;
    }
}
