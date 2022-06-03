# Command Handling

Prerequisites:
- Basic knowledge of PHP

## About Attributes

Skip to [Your First Command](#your-first-command) if you already know PHP 8's attributes.

PHP 8 added attributes, markers that can be placed on classes or methods and later found by Remark through reflection.
```php
#[Test(debug: false)]
public function myMethod(): void {}
```
PHP 8.1 attributes look a lot like comments. They start with `#[` and end with `]` and inside the brackets the name of attributes are placed. Attributes are really just classes, and inside you put parameters for their constructor. You have to make sure to import the attribute just like you would for a class.

You can have multiple attributes inside the same `#[]` structure.
```php
#[Test(debug: false), Logging(level: 2)]
public function myMethod(): void {}
```
You can also omit the parameter names, to make the attributes more concise.
```php
#[Test(false), Logging(2)]
public function myMethod(): void {}
```

## Your First Command

In Remark, you mark methods using attributes to describe how you want to take arguments from the command line.
```php
use DiamondStrider1\Remark\Command\Cmd;
use DiamondStrider1\Remark\Command\CmdConfig;
use DiamondStrider1\Remark\Command\Arg\sender;
use DiamondStrider1\Remark\Command\Arg\text;
use DiamondStrider1\Remark\Command\Guard\permission;
use pocketmine\command\CommandSender;

#[CmdConfig(
    name: 'helloworld',
    description: 'Run my hello world command!',
    aliases: ['hw'],
    permission: 'plugin.helloworld.use'
)]
class MyCommand {
    #[Cmd('helloworld'), permission('plugin.helloworld.use')]
    #[sender(), text()]
    public function myFirstCommand(CommandSender $sender, string $text): void
    {
        $sender->sendMessage("§aHello Command Handling! - $text");
    }
}
```
This is a lot of code to tackle, but before going over it let's register our command so it can be used.

## Registering Your First Command
```php
public function onEnable(): void
{
    Remark::command($this, new MyCommand());
    Remark::activate($this);
}
```

`Remark::command()` adds one or more `BoundCommand`s that implement `PluginOwned`. `Remark::activate()` registers a listener that will add `TAB`-completion for players.

## The Breakdown
Let's go over everything in `MyCommand.php`.

#### CmdConfig Attribute
```php
#[CmdConfig( /* ... */ )]
```
CmdConfig customizes the underlying command that will ultimately be registered to PocketMine's CommandMap. Everything is pretty self-explanatory. `name` is the name of the command (i. e. `/command_name`).`permission` is set as the permission of the command, hiding the command from those who have insufficient permissions. **IT DOES NOT**, however, stop people from running the command by itself. We will get into that soon.

#### Cmd and permission
```php
#[Cmd('helloworld'), permission('plugin.helloworld.use')]
```
This line uses two attributes at once. The method these attributes are placed on are called a HandlerMethod. A method simply has to be marked by `Cmd` to become a HandlerMethod.

#### Cmd Attribute
The `Cmd` is passed the name of the command. You can change the command to a subcommand by adding a comma followed by another string. For example, `#[Cmd('cmd', 'subcmd')]` would bind the method to the subcommand at `/cmd subcmd`. The command or subcommand chosen must be unique, meaning there is one HandlerMethod per command/subcommand.

#### The permission Guard
The `permission` is something Remark calls a `Guard`. `Guard`s prevent unauthorized access to commands by ensuring that certain requirements are met. If the `permission` guard fails, it will send a generic not-enough-permissions error to the command sender. The HandlerMethod only runs if all `Guard`s attached to it succeed. Unlike the `permission` property of `CmdConfig`, this guard actually enforces that the command sender has permission to using the command. More permissions can be added by adding a comma and another string, ex: `permission('perm1', 'perm2')`.

#### Args
```php
#[sender(), text()]
```

Ranked's `Arg`s have many responsibilities.
* Extracting data from command line arguments
* Validation
* Converting from string to a useful type (i. e. int)

It's required that for every `Arg` of a HandlerMethod that there is a corresponding parameter of the correct type. That parameter will be given the value extracted by the Arg whenever the command is run.

#### The sender() Arg
The `sender()` Arg requires that it's parameter is of type `CommandSender` or `Player`. It doesn't actually take any arguments from the command line, but simply supplies the command sender performing an `instanceof` check if needed.

#### The text() Arg
The `text()` Arg requires that it's parameter is of type `string`, `?string` or `array` depending on the arguments passed to it. In this case the type of the parameter must be `string`. By default `text()` takes a single string from the command line arguments, and errors if none was given.

# Asynchronous Commands
A key aspect of Remark is it's asynchronous approach to UI. This is important because of how complex asynchronous programming will become if not given care. AwaitGenerator is a virion that allows async/await style programming through Generators and their pause/resume functions. It's not required you use AwaitGenerator, but it is recommended and supported by Remark.

Here is the example extended to use Remark's first-class support of AwaitGenerator.
```php
#[Cmd('helloworld'), permission('plugin.helloworld.use')]
#[sender(), text()]
public function myFirstCommand(CommandSender $sender, string $text): Generator
{
    $sender->sendMessage("§aHello Command Handling! - $text");
    $response = yield from AsyncApi::fetch($text);
    if ($sender instanceof Player && !$sender->isConnected()) {
        return;
    }
    $sender->sendMessage("Got Response: $response");
}
```

The return type of the function is now `Generator`, and `yield from` is used to call other AwaitGenerator functions. Remember to check that the player is still connected after doing any asynchronous logic.

If you got all of that, let's move on to [forms](forms.md)!
