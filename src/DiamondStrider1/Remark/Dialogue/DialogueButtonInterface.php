<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

/**
 * @see DialogueButton
 * @see DialogueURLButton
 * @see DialogueInvisibleButton
 */
interface DialogueButtonInterface
{
    /**
     * @internal
     * @return array{button_name: string, text: string, data: ?array{cmd_line: string, cmd_line: string, cmd_ver: int}, mode: int, type: int}
     */
    public function formatAction(int $buttonIndex) : array;

    private const PROTOCOL_TYPE_URL = 0;
    private const PROTOCOL_TYPE_COMMAND = 1;
    private const PROTOCOL_CMD_VER = 17;
    private const PROTOCOL_MODE_BUTTON = 0;
    private const PROTOCOL_MODE_ON_CLOSE = 1;
    private const PROTOCOL_MODE_ON_ENTER = 0;
}
