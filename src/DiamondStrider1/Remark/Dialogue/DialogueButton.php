<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

/**
 * The basic button type. Consists of a text and command line.
 * The command line can be seen in the "Advanced Settings" page when
 * editing a dialogue.
 *
 * @see DialogueURLButton
 * @see DialogueInvisibleButton
 */
class DialogueButton implements DialogueButtonInterface
{
    public static function __construct(
        private string $text,
        private string $commandLine = ""
    ) {

    }

    /**
     * @internal
     * @return array{button_name: string, text: string, data: ?array{cmd_line: string, cmd_line: string, cmd_ver: int}, mode: int, type: int}
     */
    public function formatAction(int $buttonIndex) : array {
        return [
            "button_name" => $this->text,
            "text" => $this->commandLine,
            "data" => [
                "cmd_line" => array_map(static fn($str) => [
                    "cmd_line" => $str,
                    "cmd_ver" => self::PROTOCOL_CMD_VER
                ], explode("\n", $this->commandLine)),
            ], // TODO: test remove.
            "mode" => self::PROTOCOL_MODE_BUTTON,
            "type" => self::PROTOCOL_TYPE_COMMAND
        ];
    }
}
