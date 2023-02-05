<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialog;

/**
 * TODO: dialog button change repsonse description.
 */
class DialogButtonChangeResponse extends DialogResponse
{
    /**
     * @var DialogButton[]
     */
    public array $buttons;

    public function onAccept() : void {
        $this->dialog->mutateButtons($this->buttons);
    }

    public function onCancel() : void {
        // TODO: might need to resend.
    }
}
