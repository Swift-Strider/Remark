<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Types;

use pocketmine\math\Vector3;

final class RelativeVector3
{
    public function __construct(
        private int|float $valueX,
        private int|float $valueY,
        private int|float $valueZ,
        private bool $isOffsetX,
        private bool $isOffsetY,
        private bool $isOffsetZ,
    ) {
    }

    public function relativeTo(Vector3 $vector): Vector3
    {
        $x = $this->isOffsetX ? $vector->getX() + $this->valueX : $this->valueX;
        $y = $this->isOffsetY ? $vector->getY() + $this->valueY : $this->valueY;
        $z = $this->isOffsetZ ? $vector->getZ() + $this->valueZ : $this->valueZ;

        return new Vector3($x, $y, $z);
    }

    public function getValueX(): int|float
    {
        return $this->valueX;
    }

    public function getValueY(): int|float
    {
        return $this->valueY;
    }

    public function getValueZ(): int|float
    {
        return $this->valueZ;
    }

    public function isOffsetX(): bool
    {
        return $this->isOffsetX;
    }

    public function isOffsetY(): bool
    {
        return $this->isOffsetY;
    }

    public function isOffsetZ(): bool
    {
        return $this->isOffsetZ;
    }
}
