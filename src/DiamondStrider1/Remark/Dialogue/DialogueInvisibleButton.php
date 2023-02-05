<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

/**
 * Vanilla Minecraft misleadingly named this a "button", while it
 * basically is just a placeholder / wrapper for command line.
 * Because this "button" would not appear on the dialogue at all.
 * The command line can be seen in the "Advanced Settings" page when
 * editing a dialogue.
 *
 * TODO: Test invis button functionality.
 *
 * @see DialogueButton
 * @see DialogueURLButton
 */
class DialogueButton implements DialogueButtonInterface
{
    public const MODE_ON_ENTER = self::PROTOCOL_MODE_ON_ENTER;
    public const MODE_ON_CLOSE = self::PROTOCOL_MODE_ON_CLOSE;

    public static function __construct(
        public int $mode,
        private string $commandLine = "",
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
            ],
            "mode" => $this->mode,
            "type" => self::PROTOCOL_TYPE_COMMAND
        ];
    }
}
