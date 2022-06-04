<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Command\Arg;

use Attribute;
use DiamondStrider1\Remark\Command\CommandContext;
use DiamondStrider1\Remark\Types\RelativeVector3;
use InvalidArgumentException;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket as ACP;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Matches a Vector3 or a RelativeVector3
 * depending on the parameter type.
 *
 * @phpstan-implements Arg<Vector3|RelativeVector3|null>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
        Attribute::TARGET_METHOD
)]
final class vector_arg implements Arg
{
    use SetParameterTrait;

    private bool $optional;
    private bool $relative;

    public function setParameter(ReflectionParameter $parameter): void
    {
        $type = $parameter->getType();
        if (
            !$type instanceof ReflectionNamedType ||
            (Vector3::class !== $type->getName() &&
                RelativeVector3::class !== $type->getName()
            )
        ) {
            $name = $parameter->getName();
            throw new InvalidArgumentException("The parameter ($name) corresponding to `sender()` Arg must have a type of either CommandSender or Player!");
        }

        $this->optional = $type->allowsNull();
        $this->relative = RelativeVector3::class === $type->getName();
        $this->parameter = $parameter;
    }

    public function extract(CommandContext $context, ArgumentStack $args): Vector3|RelativeVector3|null
    {
        $component = $this->toUsageComponent($this->parameter->getName());
        if ($this->optional) {
            $x = $args->tryPop();
            $y = $args->tryPop();
            $z = $args->tryPop();
            if (null === $x || null === $y || null === $z) {
                return null;
            }
        } else {
            $x = $args->pop("Required argument $component");
            $y = $args->pop("Required argument $component");
            $z = $args->pop("Required argument $component");
        }

        if ($this->relative) {
            [$vX, $oX] = $this->extractRelativeNumber($x);
            [$vY, $oY] = $this->extractRelativeNumber($y);
            [$vZ, $oZ] = $this->extractRelativeNumber($z);

            return new RelativeVector3($vX, $vY, $vZ, $oX, $oY, $oZ);
        }

        $vX = $this->extractNumber($x);
        $vY = $this->extractNumber($y);
        $vZ = $this->extractNumber($z);

        return new Vector3($vX, $vY, $vZ);
    }

    private function extractNumber(string $numeric, ?string $arg = null): float
    {
        if (!is_numeric($numeric)) {
            $arg ??= $numeric;
            $component = $this->toUsageComponent($this->parameter->getName());
            throw new ExtractionFailed("$arg does not satisfy $component");
        }

        return (float) $numeric;
    }

    /**
     * @phpstan-return array{float, bool}
     */
    private function extractRelativeNumber(string $raw): array
    {
        $isOffset = false;
        $numeric = $raw;
        if (str_starts_with($raw, '~')) {
            $isOffset = true;
            $numeric = substr($raw, 1, strlen($raw) - 1);
        }

        return [$this->extractNumber($numeric, $raw), $isOffset];
    }

    public function toUsageComponent(string $name): ?string
    {
        if ($this->optional) {
            return "<$name: x y z>";
        } else {
            return "[$name: x y z]";
        }
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::standard($name, ACP::ARG_TYPE_POSITION, 0, $this->optional);
    }
}
