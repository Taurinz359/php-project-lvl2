<?php

namespace Differ\Formatters\Formatter;

use function Differ\Formatters\Json\getDiff;
use function Differ\Formatters\Plain\getStructDiff;
use function Differ\Formatters\Plain\getFormattedDiffTree;
use function Differ\Formatters\Stylish\Formatter\getTreeDiff;

/**
 * @throws \Exception
 */
function getDiffStruct(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => getTreeDiff($diff),
        'plain' => getStructDiff($diff),
        'json' => getDiff($diff),
        default => throw new \Exception("Incorrect format: $format")
    };
}
