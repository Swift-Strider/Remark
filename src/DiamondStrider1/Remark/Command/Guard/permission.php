<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Guard;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use InvalidArgumentException;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\Translatable;
use pocketmine\permission\PermissionManager;

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

        foreach ($this->permissions as $perm) {
            if (null === PermissionManager::getInstance()->getPermission($perm)) {
                throw new InvalidArgumentException("Cannot use non-existing permission \"$perm\"");
            }
        }
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
