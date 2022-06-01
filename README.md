# Remark - UI for PocketMine-MP plugins

<p align="center">
<img src="assets/icon.png" alt="Icon" width="262px" height="120px" />
</p>

<p align="center">
Easy and Asynchronous Commands and Forms
</p>

What you **WON'T** get:
* ❌ Boilerplate
* ❌ Fragile command handling
* ❌ Broken Forms

What you **WILL** get:
* ✔ Readable command handlers
* ✔ `TAB`-completion for commands in-game
* ✔ Async, readable forms

# Example

![Help entry](example/example-help.jpg)
![In-game autocomplete](example/example-ingame.jpg)

In `Plugin.php`:
```php
public function onEnable(): void
{
    // activate() and command() should only be called once.
    Remark::activate($this); // Allows type hints to appear in-game.
    Remark::command($this, new Commands()); // Registers the commands to the server.
}
```

In `Commands.php`:
```php
#[CmdConfig(
    name: 'myplugin',
    description: "Access my plugin through subcommands",
    aliases: [],
    permissions: ['myplugin.command']
)]
final class Commands
{
    #[Cmd('myplugin', 'showoff')]
    #[permission('myplugin.command.showoff')]
    #[sender(), enum('dance', 'dig', 'mine'), remaining()]
    public function showOff(Player $sender, string $dance, array $message): Generator
    {
        // Use AwaitGenerator to make asynchronous logic with
        // synchronous looking code!

        $message = implode(' ', $message);
        $choice2response = ['bland', 'amazing', 'AWESOME'];
        $photoDirt = 'textures/blocks/dirt.png';
        $photoGold = 'textures/blocks/gold_block.png';
        $photoAwesome = 'https://unsplash.com/photos/3k9PGKWt7ik/download?ixid=MnwxMjA3fDB8MXxhbGx8fHx8fHx8fHwxNjU0MDg0MTAy&force=true&w=640';
        do {
            // Send a menu form with a list of buttons to choose from
            $choice = yield from Forms::menu2gen(
                $sender,
                'How do you like this dance?',
                "The message you sent will be logged:\n$message",
                [
                    new MenuFormButton('bland', new MenuFormImage('path', $photoDirt)),
                    new MenuFormButton('amazing', new MenuFormImage('path', $photoGold)),
                    new MenuFormButton('awesome', new MenuFormImage('url', $photoAwesome)),
                ]
            );

            // Send a modal form that results in true or false
            $isSure = yield from Forms::modal2gen($sender, 'Are you sure though?', 'Last chance to change your mind!');
        } while (!$isSure);
        $choice = $choice !== null ? $choice2response[$choice] : "Very Undecidable";
        $sender->sendMessage("You found the dance §g$choice!");

        // Send a custom form with multiple elements
        $result = yield from MySurveyForm::custom2gen($sender, 'Want to fill out this form?');
        if (null === $result) {
            $sender->sendMessage('You chose to skip the survey!');
            return;
        }
        var_dump($result);
    }
}
```

# Terminology

`Remark::command()` finds methods marked with the
`Cmd` attribute, called HandlerMethods. A
handler methods may have `Guards` and `Args`
attached to it.

## Guard
A `Guard` prevents a HandlerMethod from being
ran when a requirement isn't satisfied. I. e.
the command sender not having permission.

## Arg
An `Arg` provides a value to it's corresponding
parameter of a HandlerMethod. The number of
`Args` in a HandlerMethod must be equal to its number of parameters. An `Arg` may extract its
value from the arguments given when the command
is run, or it may get its value from another
source.

# Attributes

## CmdConfig
Configures a base slash-command, applies to
the class.
```php
string $name,
string $description,
array $aliases = [],
array $permissions = [],
```
* name - The name of the underlying command
* description - The description of the command
* aliases - The aliases of the command
* permissions - Controls what players may see
  the command. These are **NOT** checked when
  actually running a command.

## Cmd
Marks a method as a HandlerMethod. There may
be multiple of this attribute to register
the same HandlerMethod multiple times.
```php
string $name,
string ...$subNames,
```
* name - The name of the command to attach
  this HandlerMethod to.
* subNames - Zero or more subcommand names. If
  provided

## permission
A `Guard` to prevent a HandlerMethod from
running unless the player has the needed
permissions.
```php
string $permission,
string ...$otherPermissions,
```
* permission - One required permission
* otherPermissions - Other required permissions

## sender
An `Arg` that extracts the `CommandSender`.
If it's corresponding parameter has the type
`Player` it will do an instance-of check
automatically. Otherwise the type of the
parameter must be CommandSender.

`sender()` does not take any arguments.*

## player_arg
An `Arg` that extracts a `Player` using
the name provided by a command argument.
```php
bool $exact = false,
```
* exact - whether to not match by prefix

## text
An `Arg` that extracts one or more strings
from the command arguments. Depending on the
parameters given to this Arg, the corresponding
parameter must have a type of `string`,
`?string`, or `array`.
```php
int $count = 1,
bool $require = true,
```
* count - number of arguments to take
* require - wether to fail if the number
  of arguments remaining is less than count

## remaining
An `Arg` that extracts the remaining strings
from the command arguments.

`remaining()` does not take any arguments.*

## enum
An `Arg` that extracts a string that must be
in an immutable set of predefined strings.
```php
string $name,
string $choice,
string ...$otherChoices,
```
* name - the enum's name, in-game it will show
as a command hint (i. e. `<paramName: name>`)
* choice - A possible choice
* otherChoices - Other possible choices
