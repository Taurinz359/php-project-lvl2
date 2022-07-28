<?php

namespace Differ\Differ;


use Exception;

/**
 * @param  string  $firstFile
 * @param  string  $secondFile
 * @param  string  $format
 * @return string
 * @throws Exception
 */

genDiff(__DIR__ . '/../tests/file1.json', __DIR__ . '/../tests/file2.json', '');

function genDiff(string $firstFile, string $secondFile, string $format): string
{
    [$firstFileContent, $secondFileContent] = getValidateFileContent($firstFile, $secondFile);
}

/**
 * @param  string  ...$files
 * @return array|Exception
 * @throws Exception
 */
function getValidateFileContent(string ...$files): array|Exception
{
    $content = [];
    foreach ($files as $file) {
        if (!file_exists($file)) {
            return throw new Exception("File $file don't exist");
        }
        $content[] = json_decode(file_get_contents($file), true);
    }
    return $content;
}