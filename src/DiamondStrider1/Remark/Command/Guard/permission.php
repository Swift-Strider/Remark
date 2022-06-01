<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Guard;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\Translatable;

#[Attribute(Attribute::TARGET_METHOD)]
final class permission implements Guard
{
    /** @var string[] */
    private array $permissions;

    public function __construct(
        string $permission,
        string ...$otherPermissions,
    ) {
        $this->permissions = [$permission, ...$otherPermissions];
    }

    public function passes(CommandContext $context): null|string|Translatable
    {
        foreach ($this->permissions as $permission) {
            if (!$context->sender()->hasPermission($permission)) {
                return KnownTranslationFactory::commands_generic_permission();
            }
        }

        return null;
    }
}
