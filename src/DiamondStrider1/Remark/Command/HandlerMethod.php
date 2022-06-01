<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command;

use DiamondStrider1\Remark\Command\Arg\Arg;
use DiamondStrider1\Remark\Command\Arg\ArgumentStack;
use DiamondStrider1\Remark\Command\Arg\ExtractionFailed;
use DiamondStrider1\Remark\Command\Guard\Guard;
use Generator;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat as TF;
use ReflectionMethod;
use SOFe\AwaitGenerator\Await;

/**
 * Invokes its underlying method with converted
 * arguments.
 */
final class HandlerMethod
{
    /**
     * @param Guard[] $guards
     * @param Arg[]   $args
     * @phpstan-ignore-next-line generics
     */
    public function __construct(
        private object $handler,
        private ReflectionMethod $method,
        private array $guards,
        private array $args, // @phpstan-ignore-line
    ) {
    }

    public function invoke(CommandContext $context): void
    {
        foreach ($this->guards as $guard) {
            if (null !== ($message = $guard->passes($context))) {
                if ($message instanceof Translatable) {
                    $message = $context->sender()->getLanguage()->translate($message);
                }
                $context->sender()->sendMessage(TF::RED.$message);

                return;
            }
        }

        $stack = new ArgumentStack($context->args());
        $parameters = [];
        try {
            foreach ($this->args as $arg) {
                $parameters[] = $arg->extract($context, $stack);
            }
        } catch (ExtractionFailed $e) {
            $message = $e->getTranslatable();
            if ($message instanceof Translatable) {
                $message = $context->sender()->getLanguage()->translate($message);
            }
            $context->sender()->sendMessage(TF::RED.$message);

            return;
        }

        $generator = $this->method->invokeArgs($this->handler, $parameters);
        if ($generator instanceof Generator) {
            Await::g2c($generator);
        }
    }

    /**
     * @return Arg[] parameter name => Arg
     * @phpstan-ignore-next-line generics
     */
    public function getArgs(): array
    {
        $argCount = count($this->args);
        $params = $this->method->getParameters();

        $args = [];
        for ($index = 0; $index < $argCount; ++$index) {
            $args[$params[$index]->getName()] = $this->args[$index];
        }

        return $args;
    }
}
