<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\MenuFormElement;

use JsonSerializable;

final class MenuFormButton implements JsonSerializable
{
    public function __construct(
        private string $text,
        private ?MenuFormImage $image = null,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        $data = ['text' => $this->text];
        if (null !== $this->image) {
            $data['image'] = $this->image;
        }

        return $data;
    }
}
