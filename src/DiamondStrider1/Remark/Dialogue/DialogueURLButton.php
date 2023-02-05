<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

use DiamondStrider1\Remark\Dialogue\DialogueButton;

/**
 * This button type only works on Education Edition only.
 * Endermanbugzjfc did not have a chance to test it.
 *
 * @see DialogueButton
 * @see DialogueInvisibleButton
 */
class DialogueURLButton implements DialogueButtonInterface
{
    public function __construct(
        public string $URL
    ) {

    }

    /**
     * @inheritDoc
     */
    public function formatAction(int $buttonIndex) : array {
       return [
            "button_name" => (string)$buttonIndex,
            "text" => $this->URL,
            "data" => null,
            "mode" => self::PROTOCOL_MODE_BUTTON,
            "type" => self::PROTOCOL_TYPE_URL
        ];
    }
}
