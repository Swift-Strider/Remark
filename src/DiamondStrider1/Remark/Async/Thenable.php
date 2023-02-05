<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Async;

use Closure;
use LogicException;
use Throwable;

/**
 * @phpstan-template ValueType
 * @phpstan-template ThrowableType of Throwable
 */
final class Thenable
{
    private const PENDING = 0;
    private const RESOLVED = 1;
    private const REJECTED = 2;

    /** @phpstan-var self::* */
    private int $state = self::PENDING;
    /** @phpstan-var ValueType */
    private mixed $value;
    /** @phpstan-var ThrowableType */
    private mixed $exception;

    /**
     * @var Closure[]
     * @phpstan-var (Closure(ValueType): void)[]
     */
    private array $onResolve = [];

    /**
     * @var Closure[]
     * @phpstan-var (Closure(ThrowableType): void)[]
     */
    private array $onReject = [];

    /**
     * See `Thenable::promise` for making a new Thenable.
     */
    private function __construct()
    {
    }

    /**
     * @phpstan-param Closure(ValueType): void $onResolve
     * @phpstan-param null|Closure(ThrowableType): void $onReject
     */
    public function then(Closure $onResolve, ?Closure $onReject = null): void
    {
        if (self::PENDING === $this->state) {
            $this->onResolve[] = $onResolve;
            if (null !== $onReject) {
                $this->onReject[] = $onReject;
            }
        } elseif (self::RESOLVED === $this->state) {
            $onResolve($this->value);
        } elseif (null !== $onReject) {
            $onReject($this->exception);
        }
    }

    /**
     * Creates a new Thenable using a Closure like a JavaScript Promise.
     *
     * Like in JavaScript, the $closure will be called immediately, and
     * the returned Thenable will start in a PENDING state.
     *
     * @phpstan-template V
     * @phpstan-template T of Throwable
     * @phpstan-param Closure(Closure(V): void, Closure(T): void): void $closure
     * @phpstan-return Thenable<V, T>
     */
    public static function promise(Closure $closure): self
    {
        /** @phpstan-var Thenable<V, T> $self */
        $self = new self();
        $closure(
            function (mixed $value) use ($self): void {
                if (self::PENDING !== $self->state) {
                    $ending = match ($self->state) {
                        self::RESOLVED => 'resolved!',
                        self::REJECTED => 'rejected!',
                    };
                    throw new LogicException('Attempt to resolve a promise that has already been '.$ending);
                }

                $self->value = $value;
                $self->state = self::RESOLVED;
                foreach ($self->onResolve as $onResolve) {
                    $onResolve($value);
                }
            },
            function (Throwable $exception) use ($self): void {
                if (self::PENDING !== $self->state) {
                    $ending = match ($self->state) {
                        self::RESOLVED => 'resolved!',
                        self::REJECTED => 'rejected!',
                    };
                    throw new LogicException('Attempt to resolve a promise that has already been '.$ending);
                }

                $self->exception = $exception;
                $self->state = self::REJECTED;
                if (0 === count($self->onReject)) {
                    throw new UnhandledAsyncException("An asynchronous exception wasn't caught!", previous: $exception);
                }
                foreach ($self->onReject as $onReject) {
                    $onReject($exception);
                }
            }
        );

        return $self;
    }
}
