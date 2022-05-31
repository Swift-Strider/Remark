<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark;

use DiamondStrider1\Remark\Arg\Arg;
use DiamondStrider1\Remark\Arg\ArgumentStack;
use DiamondStrider1\Remark\Arg\ExtractionFailed;
use DiamondStrider1\Remark\Guard\Guard;
use ReflectionMethod;

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
            if (!$guard->passes($context)) {
                return;
            }
        }

        $stack = new ArgumentStack($context->args());
        $parameters = [];
        try {
            foreach ($this->args as $arg) {
                $parameters[] = $arg->extract($context, $stack);
            }
        } catch (ExtractionFailed) {
            // TODO: Handle extraction failures
            return;
        }

        $this->method->invokeArgs($this->handler, $parameters);
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
