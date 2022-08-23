<?php

namespace Differ\Differ;

use function Differ\Diff\getTree;
use function Differ\Formatters\Formatter\getDiffStruct;
use function Differ\Parsers\parser;

function getContent(string $path): array
{
    $content = file_get_contents($path);

    if ($content === false) {
        throw new \Exception("File is broken");
    }

    $ext = pathinfo($path, PATHINFO_EXTENSION);

    return parser($content, $ext);
}

/**
 * @throws \Exception
 */
function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    $firstFileContent = getContent($firstFile);
    $secondFileContent = getContent($secondFile);

    $diff = getTree($firstFileContent, $secondFileContent);

    return getDiffStruct($diff, $format);
}
