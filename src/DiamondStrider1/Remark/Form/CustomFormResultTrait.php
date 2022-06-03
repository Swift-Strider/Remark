<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form;

use DiamondStrider1\Remark\Async\Thenable;
use DiamondStrider1\Remark\Form\CustomFormElement\CustomFormElement;
use DiamondStrider1\Remark\Form\CustomFormElement\Label;
use Generator;
use LogicException;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use ReflectionAttribute;
use ReflectionClass;
use SOFe\AwaitGenerator\Await;

/**
 * Classes that use this trait have the static methods,
 * `custom2gen()` and `custom2then()` that may be used
 * to send a form and construct the class when a response
 * is received.
 *
 * A class using CustomFormResultTrait must meet the following
 * requirements:
 * - Must not be abstract
 * - Every property to be filled in...
 *     - May be marked with any number of Label attributes
 *     - Must be marked with at most one CustomFormElement that isn't Label
 *
 * Properties are filled in according to the non-Label attribute
 * attached to them, or ignored if only Labels are attached to them.
 *
 * All properties without CustomFormElement attributes are ignored.
 */
trait CustomFormResultTrait
{
    /**
     * @phpstan-return Thenable<?static, FormValidationException>
     */
    public static function custom2then(Player $player, string $title): Thenable
    {
        $elements = [];
        $index2property = [];
        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $attrs = $property->getAttributes(CustomFormElement::class, ReflectionAttribute::IS_INSTANCEOF);
            $nonLabelFound = false;
            foreach ($attrs as $attr) {
                $element = $attr->newInstance();
                if (!$element instanceof Label) {
                    if ($nonLabelFound) {
                        $name = $property->getName();
                        $class = static::class;
                        throw new LogicException("Multiple non-Label element attributes on property \"$name\" of class \"$class\"");
                    }
                    $index2property[count($elements)] = $property;
                    $nonLabelFound = true;
                }
                $elements[] = $element;
            }
        }

        // @phpstan-ignore-next-line
        return Thenable::promise(function ($resolve, $reject) use ($player, $title, $elements, $index2property, $reflection) {
            $player->sendForm(new InternalCustomForm(
                function (?array $response) use ($resolve, $index2property, $reflection) {
                    if (null === $response) {
                        return null;
                    }
                    $self = $reflection->newInstanceWithoutConstructor();
                    foreach ($index2property as $index => $property) {
                        $property->setAccessible(true);
                        $property->setValue($self, $response[$index]);
                    }
                    $resolve($self);
                },
                $reject, $title, $elements
            ));
        });
    }

    /**
     * @return Generator<mixed, mixed, mixed, ?static>
     */
    public static function custom2gen(Player $player, string $title): Generator
    {
        $elements = [];
        $index2property = [];
        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $attrs = $property->getAttributes(CustomFormElement::class, ReflectionAttribute::IS_INSTANCEOF);
            $nonLabelFound = false;
            foreach ($attrs as $attr) {
                $element = $attr->newInstance();
                if (!$element instanceof Label) {
                    if ($nonLabelFound) {
                        $name = $property->getName();
                        $class = static::class;
                        throw new LogicException("Multiple non-Label element attributes on property \"$name\" of class \"$class\"");
                    }
                    $index2property[count($elements)] = $property;
                    $nonLabelFound = true;
                }
                $elements[] = $element;
            }
        }

        /** @var ?array<int, mixed> $response */
        $response = yield from Await::promise(function ($resolve, $reject) use ($player, $title, $elements) {
            $player->sendForm(new InternalCustomForm($resolve, $reject, $title, $elements
            ));
        });

        if (null === $response) {
            return null;
        }

        $self = $reflection->newInstanceWithoutConstructor();
        foreach ($index2property as $index => $property) {
            $property->setAccessible(true);
            $property->setValue($self, $response[$index]);
        }

        return $self;
    }
}
