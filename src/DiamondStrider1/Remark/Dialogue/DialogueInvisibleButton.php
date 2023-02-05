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
        private int $commandVersion = self::PROTOCOL_CMD_VER
    ) {

    }

    /**
     * @internal
     * @return array{button_name: string, text: string, data: ?array{cmd_line: string, cmd_line: string, cmd_ver: int}, mode: int, type: int}
     */
    public function formatAction(int $buttonIndex) : array {
       return [
            "button_name" => (string)$buttonIndex,
            "text" => $this->text,
            "data" => [
                "cmd_line" => $this->commandLine, // TODO: test cmd line.
                "cmd_ver" => $this->commandVersion
            ],
            "mode" => $this->mode,
            "type" => self::PROTOCOL_TYPE_COMMAND
        ];
    }

}
