<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use InvalidArgumentException;

/**
 * A stack of arguments that can be manipulated
 * by `Arg`s.
 */
final class ArgumentStack
{
    private int $cursor = 0;

    /**
     * @param string[] $args
     */
    public function __construct(
        private array $args,
    ) {
    }

    /**
     * Peek at the current element or a future element
     * without modifying the stack.
     *
     * @param int $lookahead a positive number (including zero)
     */
    public function tryPeek(int $lookahead = 0): ?string
    {
        if ($lookahead < 0) {
            throw new InvalidArgumentException('The lookahead must be greater than one!');
        }

        return $this->args[$this->cursor + $lookahead] ?? null;
    }

    /**
     * Peek at the current element or a future element
     * without modifying the stack. On failure, throws an
     * ExtractionFailed exception.
     *
     * @param int $lookahead a positive number (including zero)
     *
     * @throws ExtractionFailed
     */
    public function peek(int $lookahead = 0): string
    {
        if ($lookahead < 0) {
            throw new InvalidArgumentException('The lookahead must be greater than one!');
        }

        return $this->args[$this->cursor + $lookahead] ?? throw new ExtractionFailed();
    }

    /**
     * Get the current element and
     * advance to the next element in the stack.
     * On failure, throws an ExtractionFailed exception.
     *
     * @throws ExtractionFailed
     */
    public function tryPop(): ?string
    {
        return $this->args[$this->cursor++] ?? null;
    }

    /**
     * Get the current element and
     * advance to the next element in the stack.
     * On failure, throws an ExtractionFailed exception.
     *
     * @throws ExtractionFailed
     */
    public function pop(): string
    {
        return $this->args[$this->cursor++] ?? throw new ExtractionFailed();
    }
}
