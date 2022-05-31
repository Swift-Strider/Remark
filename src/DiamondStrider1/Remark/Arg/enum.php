<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Arg;

use Attribute;
use DiamondStrider1\Remark\CommandContext;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Matches one of a set of predefined constant strings.
 *
 * @phpstan-implements Arg<string>
 */
#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_METHOD
)]
final class enum implements Arg
{
    use SetParameterTrait;
    /** @var string[] */
    private array $choices;
    /** @var bool[] */
    private array $choiceSet;

    public function __construct(
        private string $name,
        string $choice,
        string ...$otherChoices,
    ) {
        $this->choices = [$choice, ...$otherChoices];
        $this->choiceSet = [];
        foreach ($this->choices as $c) {
            $this->choiceSet[$c] = true;
        }
    }

    public function extract(CommandContext $context, ArgumentStack $args): mixed
    {
        $choice = $args->pop();
        if (isset($this->choiceSet[$choice])) {
            return $choice;
        }
        throw new ExtractionFailed();
    }

    public function toUsageComponent(string $name): ?string
    {
        $choicesString = implode('|', $this->choices);

        return "<$name: $choicesString>";
    }

    public function toCommandParameter(string $name): ?CommandParameter
    {
        return CommandParameter::enum(
            $name, new CommandEnum($this->name, $this->choices), 0,
        );
    }
}
