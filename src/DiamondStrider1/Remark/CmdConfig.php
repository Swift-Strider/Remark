<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark;

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
     * @param string[] $permissions
     */
    public function __construct(
        private string $name,
        private string $description,
        private array $aliases = [],
        private array $permissions = [],
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

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->permissions;
    }
}
