<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

/**
 * TODO: dialogue button change repsonse description.
 */
class DialogueButtonChangeResponse extends DialogueResponse
{
    /**
     * @var DialogueButton[]
     */
    public array $buttons;

    public function onAccept() : void {
        $this->dialogue->mutateButtons($this->buttons);
    }

    public function onCancel() : void {
        // TODO: might need to resend.
    }
}
