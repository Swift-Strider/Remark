<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use ReflectionParameter;

trait SetParameterTrait
{
    private ReflectionParameter $parameter;

    public function setParameter(ReflectionParameter $parameter): void
    {
        $this->parameter = $parameter;
    }
}
