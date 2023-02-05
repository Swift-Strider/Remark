<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialogue;

/**
 * Prohibiting repeat calls to any methods in this class to enforce memory safety.
 */
abstract class DialogueResponse
{

    public function __construct(
        protected Dialogue $dialogue,
        private \Closure $unlocker
    ) {

    }

    public function accept() : Dialogue {
        $this->onAccept();
        $this->unlock();
        return $this->dialogue;
    }

    public function cancel() : Dialogue {
        $this->onCancel();
        $this->unlock();
        return $this->dialogue;
    }

    abstract protected function onAccept() : void;
    abstract protected function onCancel() : void;

    private function unlock() : void {
        if (!isset($this->unlocker)) {
            throw new DialogueException("Repeat calls of DialogueResponse::cancel() has have been prohibited to enforce memory safety.");
        }
        ($this->unlocker)();
        unset($this->unlocker);
    }
}
