# In Depth | Commands

`Remark::command()` is used to bind the HandlerMethods of an object to a CommandMap. By default, `Remark::command()` uses the CommandMap attached to the running PocketMine Server. Under-the-hood, a new `BoundCommand` is made for every `CmdConfig`, and they implement `PluginOwned` returning the plugin passed to `Remark::command()`.

`Remark::activate()` registers a listener which will add `TAB`-completion for players.

## CmdConfig
Configures the commands of a handler object.
```php
string $name,
string $description,
array $aliases = [],
?string $permission = null,
```
* name - The name of the underlying command
* description - The description of the command
* aliases - The aliases of the command
* permission - If set, one or more permissions separated by `;`

## Cmd
Marks a method as a handler for a command. You may bind a HandlerMethod to multiple commands by repeating this attribute.
```php
string $name,
string ...$subNames,
```
* name - The name of the command to attach this HandlerMethod to
* subNames - Zero or more subcommand names
