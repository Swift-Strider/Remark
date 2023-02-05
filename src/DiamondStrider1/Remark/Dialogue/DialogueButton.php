<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

class DialogueButton implements \JsonSerializable
{
    public function __construct(
    ) {

    }

    public function jsonSerialize() : array {
        return [
            "button_name" => $this->name,
            "text" => $this->text,
            "data" => $this->data,
            "mode" => $this->mode,
            "type" => $this->type
        ];
    }

    /**
     * ...
     * @return self[]
     * @throws \JsonException
     */
    public static function jsonUnserializeMulti(string $json) : array {
        $actionData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
