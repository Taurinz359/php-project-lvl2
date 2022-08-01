<?php

namespace Differ\Differ;


use Exception;

const SAME_VALUE = 1;
const DIFFERENT_VALUE = 2;
const FIRST_VALUE_NOT_EXIST = 3;
const SECOND_VALUES_NOT_EXIST = 4;
const ERROR_IN_FUNCTION = 5;

//genDiff(__DIR__ . '/../tests/file1.json', __DIR__ . '/../tests/file2.json', '');

function genDiff(string $firstFile, string $secondFile, string $format): string
{
    [$firstFileContent, $secondFileContent] = getValidateFileContent($firstFile, $secondFile);
    ksort($firstFileContent);
    ksort($secondFileContent);

    $keys = array_merge(array_keys($firstFileContent), array_keys($secondFileContent));
    $keys = array_values(array_map(null, array_unique($keys)));

    $structure = checkFileKeyValue($keys, $firstFileContent, $secondFileContent);
    return createDiff($structure);
}

/**
 * @param  array<int>  $structure
 */

function createDiff(array $structure): string
{
    $structure = array_reduce($structure, function ($acc, $item) {
        $acc[] = getDiffString($item);
        return $acc;
    });

    $firstBrace = "{\n";
    $secondBrace = "\n}\n";
    return $firstBrace . implode("\n", $structure) . $secondBrace;
}

function getDiffString(array $file): string
{
    $valueType = $file['valueType'];
    $key = $file['key'];
    $firstValue = !$file['firstValue'] ? "false" : $file['firstValue'];
    $secondValue = json_encode($file['secondValue']);
    //todo : Решить проблему с вэлью, так-же сделать stan анализ

    return match ($valueType) {
        SAME_VALUE => "\t   $key: $firstValue",
        DIFFERENT_VALUE => "\t - $key: $firstValue\n\t + $key: $secondValue",
        SECOND_VALUES_NOT_EXIST => "\t - $key: $firstValue",
        FIRST_VALUE_NOT_EXIST => "\t + $key: $secondValue"
    };
}

function getValidateFileContent(string ...$files): array|string
{
    $content = [];
    foreach ($files as $file) {
        if (!file_exists($file)) {
            echo "File $file don't exist";
            exit();
        }
        $content[] = json_decode(file_get_contents($file), true);
    }
    return $content;
}

function checkFileKeyValue(array $keys, array $firstFile, array $secondFile): array
{
    return array_map(function ($key) use ($firstFile, $secondFile) {
        $firstFileValue = array_key_exists($key, $firstFile) ? $firstFile[$key] : null;
        $secondFileValue = array_key_exists($key, $secondFile) ? $secondFile[$key] : null;
        return [
            'key' => $key,
            'firstValue' => $firstFileValue,
            'secondValue' => $secondFileValue,
            'valueType' => valueIsUnchanged($firstFileValue, $secondFileValue)
        ];
    }, $keys);
}

function valueIsUnchanged($firstValue, $secondValue): int
{
    if ($firstValue === $secondValue) {
        return SAME_VALUE;
    } elseif ($firstValue !== null && $secondValue !== null && $firstValue !== $secondValue) {
        return DIFFERENT_VALUE;
    } elseif ($firstValue === null) {
        return FIRST_VALUE_NOT_EXIST;
    } elseif ($secondValue === null) {
        return SECOND_VALUES_NOT_EXIST;
    }

    return ERROR_IN_FUNCTION;
}