<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use ReflectionParameter;

trait SetParameterTrait
{
    /** @var bool wether the parameter is optional */
    private bool $optional;
    private ReflectionParameter $parameter;

    public function setParameter(ReflectionParameter $parameter): void
    {
        $this->optional = $parameter->getType()->allowsNull() ?? false;
        $this->parameter = $parameter;
    }
}
