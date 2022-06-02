<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use Attribute;

/**
 * Configures the commands of a handler object.
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_CLASS
)]
final class CmdConfig
{
    /**
     * @param string[] $aliases
     * @param string   $permission If set, one or more permissions separated by `;`
     */
    public function __construct(
        private string $name,
        private string $description,
        private array $aliases = [],
        private ?string $permission = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function aliases(): array
    {
        return $this->aliases;
    }

    public function permission(): ?string
    {
        return $this->permission;
    }
}
