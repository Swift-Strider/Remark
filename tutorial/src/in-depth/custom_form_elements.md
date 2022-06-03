# Custom Form Elements

`Dropdown`, `Input`, `Label`, `Slider`, `StepSlider`, and `Toggle` are found in the `DiamondStrider1\Remark\Forms\CustomFormElement` namespace and all implement `CustomFormElement`. They may be used as normal classes (`new Label('Some Text')`) or as attributes (`#[Label('Some Text')]`).

Information on creating custom forms can be found [here](forms.md#custom-form).

## Dropdown
Returns an integer which is the index of the choice the player selected.
```php
string $text,
array $options,
int $defaultOption = 0,
bool $allowDefault = true,
```
* allowDefault - whether the player may skip filling out a dropdown when the Dropdown's default value is -1

## Input
Returns a string that the player entered.
```php
string $text,
string $placeholder = '',
string $default = '',
```

## Label
Does not return anything, but it does place text at its location.
```php
string $text
```

## Slider
Returns a float in within the range [min, max]. It **DOES NOT** validate the step, however, so that responsibility is left to the developer.
```php
string $text,
float $min,
float $max,
float $step = 1.0,
?float $default = null,
```

## StepSlider
Returns an integer, the index of the step the player chose. Visually, looks like a Slider but the player chooses one of the steps.
```php
string $text,
array $steps,
int $defaultOption = 0,
```
* steps - list of strings to choose from

## Toggle
Returns a boolean. Creates a switch that the player can toggle.
```php
string $text,
bool $default = false,
```
