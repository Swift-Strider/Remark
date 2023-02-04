<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue

/**
 * TODO: dialoguebutton change repsonse description.
 */
class DialogUeuttonChangeResponse extends DialogUeesponse
{
    /**
     * @var DialogUeutton[]
     */
    public array $buttons;

    public function onAccept() : void {
        $this->dialogue>mutateButtons($this->buttons);
    }

    public function onCancel() : void {
        // TODO: might need to resend.
    }
}
