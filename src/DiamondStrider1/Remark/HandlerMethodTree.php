<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark;

/**
 * A tree of `HandlerMethod`s with a root at
 * every level of nesting.
 */
final class HandlerMethodTree
{
    private ?HandlerMethod $root = null;
    /** @var HandlerMethodTree[] */
    private array $children = [];

    /**
     * @param string[] $subNames
     */
    public function add(array $subNames, HandlerMethod $handlerMethod): void
    {
        if (0 === count($subNames)) {
            $this->root = $handlerMethod;

            return;
        }

        $name = array_shift($subNames);
        if (!isset($this->children[$name])) {
            $this->children[$name] = new self();
        }
        $this->children[$name]->add($subNames, $handlerMethod);
    }

    /**
     * @param string[] $subNames
     * @param string[] $unusedNames
     */
    public function getMostNested(array $subNames, array &$unusedNames): ?HandlerMethod
    {
        if (0 === count($subNames)) {
            $unusedNames = [];

            return $this->root;
        }

        $name = array_shift($subNames);
        $child = $this->children[$name] ?? null;
        $fetched = null === $child ? null : $child->getMostNested($subNames, $unusedNames);

        if (null === $fetched && null !== $this->root) {
            $unusedNames = [$name, ...$subNames];

            return $this->root;
        }

        return $fetched;
    }
}
