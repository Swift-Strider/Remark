<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialog;

class DialogButtonPressResponse extends DialogResponse
{
    public DialogButton $button;

    // Does nothing other than unlocking the dialog:
    public function onAccept() : void {}
    public function onCancel() : void {}
}
