# Forms

Prerequisites:
- Basic knowledge of PHP
- Basic knowledge of PHP 8's attributes

The main way to send forms to players is through Remark's `DiamondStrider1\Remark\Form\Forms` class. It contains the methods `modal2then()`, `modal2gen()`, `menu2then()`, `menu2gen()`, `custom2then()`, and `custom2gen()`. Methods ending in `gen` return an AwaitGenerator compatible generator that sends the form of its type and returns it's result. Methods ending in `then` exist in case AwaitGenerator isn't available, and return Thenable's that are resolved with the form's result.

Let's continue the example from [Command Handling](command_handling.md).
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
For those not familiar with Remark's [command handling](command_handling.md), the method `myFirstCommand()` will be ran whenever a player (or the console) runs `/helloworld` and has the permission `plugin.helloworld.use`.

## ModalForm
Starting off simple, we will send the player a yes/no modal form. Modal forms cannot be closed out of, if you try to hit `ESC` the game doesn't acts as if you had pressed the no button.
```php
/** @var bool $choice */
$choice = yield from Forms::modal2gen(
    $sender,
    'What is your favorite ice cream?',
    'Pick from these ice cream flavors.',
);
$choice = match ($choice) {
    true => '§aYes',
    false => '§cNo',
};
$sender->sendMessage("You said {$choice}§r.");
```
Notice we don't have to check if the player's online because when a PocketMine guarantees the player is still connected when a form is submitted.

## MenuForm
Let's now give the player a menu form with options to choose from.
```php
/** @var ?int $choice */
$choice = yield from Forms::menu2gen(
    $sender,
    'What is your favorite ice cream?',
    'Pick from these ice cream flavors.',
    [
        new MenuFormButton('Vanilla'),
        new MenuFormButton('Blueberry'),
        new MenuFormButton('Lime'),
    ]
);
if (null !== $choice) {
    $choice = ['vanilla', 'blueberry', 'lime'][$choice];
    $sender->sendMessage("You chose §g{$choice}§r.");
}
```
A MenuFormButton can also have an attached MenuFormImage like so.
```php
new MenuFormButton('My Button', new MenuFormImage(type: 'url', location: 'https://my.image.com/image'))
new MenuFormButton('My Button', new MenuFormImage(type: 'path', location: 'textures/blocks/dirt.png'))
```

## CustomForm
Now for the most complex type of form. A custom form sends a list of elements for the player to fill out and a submit button to press when the player is finished. Remark gives you two ways to create a custom form. First we will start with the `custom2gen()`/`custom2then()` method.
```php
/** @var array<int, mixed> $response */
$response = yield from Forms::custom2gen(
    $sender,
    'What is your favorite ice cream?',
    [
        new Label('Type a valid ice cream flavor!'),
        new Input('Ice Cream Flavor', placeholder: 'Vanilla'),
    ]
);
$sender->sendMessage("You said {$response[1]}§r.");
```
Using this method of sending custom forms, you have to index the response data with the index of the element. Remark already handles the validation for you.

Another way to make custom forms is through attributes.
```php
use DiamondStrider1\Remark\Form\CustomFormElement\Label;
use DiamondStrider1\Remark\Form\CustomFormElement\Input;
use DiamondStrider1\Remark\Form\CustomFormResultTrait;

final class MySurveyForm
{
    use CustomFormResultTrait;

    #[Label('Type a valid ice cream flavor!')]
    #[Input('Ice Cream Flavor', placeholder: 'Vanilla')]
    public string $name;
}
```
```php
/** @var MySurveyForm $formResult */
$formResult = yield from MySurveyForm::custom2gen(
    $sender, 'What is your favorite ice cream?'
);
$sender->sendMessage("You said {$formResult->name}§r.");
```
The CustomFormResultTrait adds the static functions `custom2gen()` and `custom2then()` that take a player and a title for the form. It's recommended that you mark form fields as public so they are easily accessible.
