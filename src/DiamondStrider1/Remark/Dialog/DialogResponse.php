<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Dialog;

/**
 * Prohibiting repeat calls to any methods in this class to enforce memory safety.
 */
abstract class DialogResponse
{

    public function __construct(
        protected Dialog $dialog,
        private \Closure $unlocker
    ) {

    }

    public function accept() : Dialog {
        $this->onAccept();
        $this->unlock();
        return $this->dialog;
    }

    public function cancel() : Dialog {
        $this->onCancel();
        $this->unlock();
        return $this->dialog;
    }

    abstract protected function onAccept() : void;
    abstract protected function onCancel() : void;

    private function unlock() : void {
        if (!isset($this->unlocker)) {
            throw new DialogException("Repeat calls of DialogResponse::cancel() has have been prohibited to enforce memory safety.");
        }
        ($this->unlocker)();
        unset($this->unlocker);
    }
}
