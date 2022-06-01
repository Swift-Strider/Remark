<?php

declare(strict_types=1);

namespace example;

use DiamondStrider1\Remark\Form\CustomFormElement\Dropdown;
use DiamondStrider1\Remark\Form\CustomFormElement\Input;
use DiamondStrider1\Remark\Form\CustomFormElement\Label;
use DiamondStrider1\Remark\Form\CustomFormElement\Slider;
use DiamondStrider1\Remark\Form\CustomFormElement\StepSlider;
use DiamondStrider1\Remark\Form\CustomFormElement\Toggle;
use DiamondStrider1\Remark\Form\CustomFormResultTrait;

final class MySurveyForm
{
    use CustomFormResultTrait;

    #[Dropdown('Best Game?', ['MyGame', 'YourGame'], -1)] private int $bestGame;
    #[Input('What is your name?')] private string $name;
    #[Label('A label'), Label('Another One')] private string $aLabel;
    #[Slider('level', 0, 5)] private int $level;
    #[StepSlider('World:', ['mine', 'yours'])] private int $world;
    #[Label('Important!'), Toggle('On?', true), Label('Important^')] private bool $on;
}
