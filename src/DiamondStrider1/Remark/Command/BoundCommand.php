<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\permission\Permissible;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;

/**
 * A registered command that dispatches to `HandlerMethod`s.
 *
 * BoundCommand runs the most deeply nested and applicable HandlerMethod.
 */
final class BoundCommand extends Command implements PluginOwned
{
    private HandlerMethodTree $map;
    private OverloadMap $overloads;

    /**
     * @param string[] $aliases
     * @param string[] $permissions
     */
    public function __construct(
        private Plugin $plugin,
        string $name,
        string $description,
        array $aliases,
        private array $permissions,
    ) {
        parent::__construct($name, $description, null, $aliases);
        $this->map = new HandlerMethodTree();
        $this->overloads = new OverloadMap($name);
    }

    /**
     * @param string[] $subNames
     */
    public function attach(array $subNames, HandlerMethod $handlerMethod): void
    {
        $this->map->add($subNames, $handlerMethod);
        $this->overloads->add($subNames, $handlerMethod);
        $this->setUsage($this->overloads->getUsage());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $newArgs = [];
        $handlerMethod = $this->map->getMostNested($args, $newArgs);
        if (null === $handlerMethod) {
            $message = $sender->getLanguage()->translate(
                KnownTranslationFactory::commands_generic_usage($this->getUsage())
            );
            $sender->sendMessage(TF::RED.$message);

            return;
        }
        $handlerMethod->invoke(new CommandContext($sender, $newArgs));
    }

    public function checkVisibility(Permissible $permissible): bool
    {
        foreach ($this->permissions as $perm) {
            if (!$permissible->hasPermission($perm)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return CommandParameter[][]
     */
    public function getOverloads(): array
    {
        return $this->overloads->getOverloads();
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }
}
