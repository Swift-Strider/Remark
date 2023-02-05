<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

class DialogueButtonPressResponse extends DialogueResponse
{
    public DialogueButton $button;

    // Does nothing other than unlocking the dialogue:
    public function onAccept() : void {}
    public function onCancel() : void {}
}
