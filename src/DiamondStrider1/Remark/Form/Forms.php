<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form;

use DiamondStrider1\Remark\Async\Thenable;
use DiamondStrider1\Remark\Form\CustomFormElement\CustomFormElement;
use DiamondStrider1\Remark\Form\MenuFormElement\MenuFormButton;
use Generator;
use InvalidArgumentException;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

/**
 * Static functions to send menu and modal forms to players.
 *
 * `Forms::custom2gen()` should be used over `Forms::custom2then()`
 * for making custom forms when "sof3/await-generator" is available.
 * An alternative to the two above methods is the CustomFormResultTrait,
 * more info can be found at it's file.
 *
 * `Forms::modal2gen()` and `Forms::menu2gen()` are the
 * recommended methods for creating forms as they both use
 * AwaitGenerator to facilitate their concise syntax.
 * `Forms::modal2then()` and `Forms::menu2then()` exist to provide
 * an alternative to AwaitGenerator for whatever reason.
 *
 * Like how one should expect `Forms::modal2gen()`
 * and `Forms::menu2gen()` don't send a form to the player until
 * `yield from` is used or are put in Await::g2c().
 *
 * In contrast, `Forms::modal2then()` and `Forms::menu2then()`
 * immediately send a form to the player and return a Thenable
 * that can be then()'ed to get the player's answer.
 */
final class Forms
{
    /**
     * Sends a modal form that consists of a title,
     * content, and two buttons. The player must choose either
     * yes or no.
     *
     * This function returns a Thenable that may be used when
     * `sof3/await-generator` is not present. Otherwise it's
     * recommended to use `Forms::modal2gen()` because of
     * AwaitGenerator's simpler syntax.
     *
     * @phpstan-return Thenable<bool, FormValidationException>
     */
    public static function modal2then(
        Player $player,
        string $title,
        string $content,
        string $yesText = 'gui.yes',
        string $noText = 'gui.no',
    ): Thenable {
        /* @phpstan-ignore-next-line */
        return Thenable::promise(function ($resolve, $reject) use ($player, $title, $content, $yesText, $noText) {
            $player->sendForm(new InternalModalForm($resolve, $reject, $title, $content, $yesText, $noText));
        });
    }

    /**
     * Sends a modal form that consists of a title,
     * content, and two buttons. The player must choose either
     * yes or no.
     *
     * This function returns a generator suitable with
     * `sof3/await-generator`, and the generator returns true
     * or false according to the player's choice.
     *
     * @return Generator<mixed, mixed, mixed, bool>
     */
    public static function modal2gen(
        Player $player,
        string $title,
        string $content,
        string $yesText = 'gui.yes',
        string $noText = 'gui.no',
    ): Generator {
        /** @var bool $response */
        $response = yield from Await::promise(function ($resolve, $reject) use ($player, $title, $content, $yesText, $noText) {
            $player->sendForm(new InternalModalForm($resolve, $reject, $title, $content, $yesText, $noText));
        });

        return $response;
    }

    /**
     * Sends a menu form that consists of a title,
     * content, and a number of buttons. The player
     * must choose a single button. The array of buttons
     * passed to this function must be a list, meaning its
     * first entry's key is zero, the next being one, etc.
     *
     * This function returns a Thenable that may be used when
     * `sof3/await-generator` is not present. Otherwise it's
     * recommended to use `Forms::menu2gen()` because of
     * AwaitGenerator's simpler syntax.
     *
     * @param array<int, MenuFormButton> $buttons
     * @phpstan-return Thenable<?int, FormValidationException>
     */
    public static function menu2then(
        Player $player,
        string $title,
        string $content,
        array $buttons,
    ): Thenable {
        // @phpstan-ignore-next-line
        if ([] !== $buttons || $buttons !== array_values($buttons)) {
            $expected = 0;
            foreach (array_keys($buttons) as $index) {
                if ($index !== $expected++) {
                    throw new InvalidArgumentException('The passed array of buttons is not a list!');
                }
            }
        }
        /* @phpstan-ignore-next-line */
        return Thenable::promise(function ($resolve, $reject) use ($player, $title, $content, $buttons) {
            $player->sendForm(new InternalMenuForm(
                $resolve, $reject, $title, $content, $buttons
            ));
        });
    }

    /**
     * Sends a menu form that consists of a title,
     * content, and a number of buttons. The player
     * must choose a single button. The array of buttons
     * passed to this function must be a list, meaning its
     * first entry's key is zero, the next being one, etc.
     *
     * This function returns a generator suitable with
     * `sof3/await-generator`, and the generator returns the
     * index of the button chosen by the player.
     *
     * @param array<int, MenuFormButton> $buttons
     *
     * @return Generator<mixed, mixed, mixed, ?int>
     */
    public static function menu2gen(
        Player $player,
        string $title,
        string $content,
        array $buttons,
    ): Generator {
        // @phpstan-ignore-next-line
        if ([] !== $buttons || $buttons !== array_values($buttons)) {
            $expected = 0;
            foreach (array_keys($buttons) as $index) {
                if ($index !== $expected++) {
                    throw new InvalidArgumentException('The passed array of buttons is not a list!');
                }
            }
        }
        /** @var ?int $response */
        $response = yield from Await::promise(function ($resolve, $reject) use ($player, $title, $content, $buttons) {
            $player->sendForm(new InternalMenuForm(
                $resolve, $reject, $title, $content, $buttons
            ));
        });

        return $response;
    }

    /**
     * Sends a custom form that consists of a title and
     * an array of elements. The player may fill in multiple
     * elements before pressing the submit button.
     *
     * This function returns a Thenable that may be used when
     * `sof3/await-generator` is not present. Otherwise it's
     * recommended to use `Forms::menu2gen()` because of
     * AwaitGenerator's simpler syntax.
     *
     * @param array<int, CustomFormElement> $elements
     * @phpstan-return Thenable<?array<int, mixed>, FormValidationException>
     */
    public static function custom2then(
        Player $player,
        string $title,
        array $elements,
    ): Thenable {
        // @phpstan-ignore-next-line
        if ([] !== $elements || $elements !== array_values($elements)) {
            $expected = 0;
            foreach (array_keys($elements) as $index) {
                if ($index !== $expected++) {
                    throw new InvalidArgumentException('The passed array of elements is not a list!');
                }
            }
        }
        /* @phpstan-ignore-next-line */
        return Thenable::promise(function ($resolve, $reject) use ($player, $title, $elements) {
            $player->sendForm(new InternalCustomForm(
                $resolve, $reject, $title, $elements
            ));
        });
    }

    /**
     * Sends a custom form that consists of a title and
     * an array of elements. The player may fill in multiple
     * elements before pressing the submit button.
     *
     * This function returns a generator suitable with
     * `sof3/await-generator`, and the generator returns the
     * index of the button chosen by the player.
     *
     * @param array<int, CustomFormElement> $elements
     *
     * @return Generator<mixed, mixed, mixed, ?array<int, mixed>>
     */
    public static function custom2gen(
        Player $player,
        string $title,
        array $elements,
    ): Generator {
        // @phpstan-ignore-next-line
        if ([] !== $elements || $elements !== array_values($elements)) {
            $expected = 0;
            foreach (array_keys($elements) as $index) {
                if ($index !== $expected++) {
                    throw new InvalidArgumentException('The passed array of elements is not a list!');
                }
            }
        }
        /** @var ?array<int, mixed> $response */
        $response = yield from Await::promise(function ($resolve, $reject) use ($player, $title, $elements) {
            $player->sendForm(new InternalCustomForm(
                $resolve, $reject, $title, $elements
            ));
        });

        return $response;
    }
}
