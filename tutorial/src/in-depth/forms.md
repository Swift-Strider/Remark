# In Depth | Forms
The `DiamondStrider1\Remark\Form\Forms` class holds static methods for creating forms. They are `modal2then()`, `modal2gen()`, `menu2then()`, `menu2gen()`, `custom2then()`, and `custom2gen()`. The `*2gen()` methods return generators to be used with await-generator, and the `*2then()` methods return `Thenable` a type defined by Remark.

# Thenable
A `Thenable` is much like a promise. One may call `$thenable->then($onResolve, $onReject)`. If you omit `$onReject` Remark will throw an `UnhandledAsyncException` if the `Thenable` is rejected.

# Modal Form
Use either `Forms::modal2then()` or `Forms::modal2gen()`. Resolves with a boolean, `true` if the yes button was hit, `false` if the no button was.
```php
Player $player,
string $title,
string $content,
string $yesText = 'gui.yes',
string $noText = 'gui.no',
```

# Menu Form
Use either `Forms::menu2then()` or `Forms::menu2gen()`. Resolves with an integer, the index of the button the player chose.
```php
Player $player,
string $title,
string $content,
array $buttons,
```
* buttons - A list of `MenuFormButton`s

## MenuFormButton
A button for a menu form.
```php
string $text,
?MenuFormImage $image = null,
```
* text - The text of the button
* image - An optional image to display

## MenuFormImage
An image that can be to present on a menu form.
```php
string $type,
string $location,
```
* type - Either 'url' or 'path'.
* location - The location of the image

If the image's type is `url`, Minecraft will
fetch the image from online, and it may take
some time to load. If the type is `path`,
Minecraft will instantly load the image from
the resource pack.

On Minecraft Windows 10, `url` images may not
show until ALT-TAB'ing out of then back into Minecraft.

An example location of a `path` type image is
"textures/block/dirt.png" without the leading
slash.

# Custom Form
You may use `Forms::custom2then()`/`Forms::custom2gen()` or `CustomFormResultTrait`.

## Custom Form, static functions
Returns an array with the indexes of elements mapped to the values of the player's response.
```php
Player $player,
string $title,
array $elements,
```
* elements - A list of `CustomFormElement`s

## Custom Form, CustomFormResultTrait

A class that uses this trait will have the static methods `custom2gen()` and `custom2then()` added which return a new instance of the class instead of an array.

A class using CustomFormResultTrait must meet the following
requirements:
- Must not be abstract
- Every property to be filled in...
    - May be marked with any number of Label attributes
    - Must be marked with at most one CustomFormElement that isn't Label

Properties are filled in according to the non-Label attribute
attached to them, or ignored if only Labels are attached to them.

All properties without CustomFormElement attributes are ignored.
