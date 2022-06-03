<?php

declare(strict_types=1);

namespace DiamondStrider1\Remark\Form\MenuFormElement;

use JsonSerializable;

/**
 * An image to present on a menu form.
 *
 * The image's type may be either url or path.
 * If the image's type is `url`, Minecraft will
 * fetch the image from online, and it may take
 * some time to load. When the type is `path`,
 * Minecraft will instantly load the image from
 * the resource pack.
 *
 * On Minecraft Windows 10, `url` images may not
 * show until ALT-TAB'ing out then back in Minecraft.
 *
 * An example location of a `path` type image is
 * "textures/block/dirt.png" without the leading
 * slash.
 */
final class MenuFormImage implements JsonSerializable
{
    public const TYPE_URL = 'url';
    public const TYPE_RESOURCES_PATH = 'path';

    /**
     * An example location of a `path` type image is
     * "textures/block/dirt.png" without the leading
     * slash.
     *
     * @param string $type     Either 'url' or 'path'
     * @param string $location A URL or a path in one
     *                         of the client's resource packs
     *
     * @phpstan-param self::TYPE_* $type
     */
    public function __construct(
        private string $type,
        private string $location,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'data' => $this->location,
        ];
    }
}
