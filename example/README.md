# Example

This example plugin registers a player-only command that sends a modal, menu, and custom form.

![Help entry](example-help.jpg)
![In-game autocomplete](example-ingame.jpg)

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
