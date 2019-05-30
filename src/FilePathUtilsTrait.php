<?php

namespace DeskPRO\Hab;

/**
 * Trait FilePathUtilsTrait
 *
 * @package DeskPRO\Hab
 */
trait FilePathUtilsTrait
{
    /**
     * @param array $dirs
     * @param bool $hasEnclosingSeparators
     * @return string
     */
    protected static function createPath(array $dirs = [], bool $hasEnclosingSeparators = true): string
    {
        return ($hasEnclosingSeparators ? DIRECTORY_SEPARATOR : '')
            .implode(DIRECTORY_SEPARATOR, $dirs)
            .($hasEnclosingSeparators && count($dirs) ? DIRECTORY_SEPARATOR : '')
        ;
    }
}
