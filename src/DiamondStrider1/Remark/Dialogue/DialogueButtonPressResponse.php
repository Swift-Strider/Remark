<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue

class DialogUeuttonPressResponse extends DialogUeesponse
{
    public DialogUeutton $button;

    // Does nothing other than unlocking the dialogue
    public function onAccept() : void {}
    public function onCancel() : void {}
}
