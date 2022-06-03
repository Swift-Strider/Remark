# Command Args
An `Arg` provides a value to its corresponding parameter of a HandlerMethod. The number of `Args` in a HandlerMethod must be equal to its number of parameters. An `Arg` may extract its value from the arguments given when the command is run, or it may get its value from another source.

## sender
Extracts the `CommandSender`. If its corresponding parameter has the type `Player` it will do an instance-of check automatically. Otherwise the type of the parameter must be CommandSender.

*`sender()` does not take any arguments.*

## player_arg
Extracts a `Player` using the name provided by a command argument.
```php
bool $exact = false,
```
* exact - whether to not match by prefix

## text
Extracts one or more strings from the command arguments. Depending on the parameters given to this Arg, the corresponding parameter must have a type of `string`, `?string`, or `array`.
```php
int $count = 1,
bool $require = true,
```
* count - number of arguments to take
* require - wether to fail if the number
  of arguments remaining is less than count

## remaining
Extracts the remaining strings from the command arguments.

*`remaining()` does not take any arguments.*

## enum
Extracts a string that must be in an immutable set of predefined strings.
```php
string $name,
string $choice,
string ...$otherChoices,
```
* name - the enum's name, in-game it will show
as a command hint (i. e. `<paramName: name>`)
* choice - A possible choice
* otherChoices - Other possible choices

## bool_arg
Extracts a true / false boolean.
Valid command arguments for both choices are:
- true: "true", "on", and "yes"
- false: "false", "off", and "no"

*`bool_arg()` does not take any arguments.*

## int_arg
Extracts an integer.

*`int_arg()` does not take any arguments.*

## float_arg
Extracts a float.

*`float_arg()` does not take any arguments.*

## vector_arg
Extracts either a Vector3 or RelativeVector3 depending on the type of it's corresponding parameter.

If your parameter has the type RelativeVector3, you can then call `$relativeVector->relativeTo($vector)` to get a real Vector3.

*`vector_arg()` does not take any arguments.*

## json_arg
Extracts a string **WITHOUT** validating that it's proper json. This is more of a marker, telling the player's client that JSON is needed. You **MUST** verify that the JSON is valid, yourself.

*`json_arg()` does not take any arguments.*

## command_arg
Extracts a string **WITHOUT** validating that it's the name of a command. This is more of a marker, telling the player's client that a command name is needed. You **MUST** verify that the command name is valid, yourself.

*`command_arg()` does not take any arguments.*
