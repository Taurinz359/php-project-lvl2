<?php

namespace Differ\Differ;

use function Functional\sort as sortFunc;

const SAME_VALUE = 1;
const DIFFERENT_VALUE = 2;
const FIRST_VALUE_NOT_EXIST = 3;
const SECOND_VALUES_NOT_EXIST = 4;
const ERROR_IN_FUNCTION = 5;

/**
 * @throws \ErrorException
 */
function genDiff(string $firstFile, string $secondFile, string $format): string
{
    [$firstFileContent, $secondFileContent] = getValidateFileContent($firstFile, $secondFile);
    $firstFileSorted = sortFunc($firstFileContent, function ($left, $right, $array) {
        return strcmp(array_search($left, $array), array_search($right, $array));
    }, true);
    $secondFileSorted = sortFunc($secondFileContent, function ($left, $right, $array) {
        return strcmp(array_search($right, $array), array_search($left, $array));
    }, true);

    $keys = array_merge(array_keys($firstFileSorted), array_keys($secondFileSorted));
    $uniqKeys = array_values(array_map(null, array_unique($keys)));

    $structure = getStructureByKeys($uniqKeys, $firstFileContent, $secondFileContent);
    return createDiff($structure);
}

/**
 * @param array<int> $structure
 */

function createDiff(array $structure): string
{
    $structure = array_map(function ($item) {
        return getDiffString((array)$item);
    }, $structure);

    $firstBrace = "{\n";
    $secondBrace = "\n}";
    return $firstBrace . implode("\n", $structure) . $secondBrace;
}

function getDiffString(array $file): string
{
    $valueType = $file['valueType'];
    $key = $file['key'];
    $firstValue = is_string($file['firstValue']) ? $file['firstValue'] : json_encode($file['firstValue']);
    $secondValue = is_string($file['secondValue']) ? $file['secondValue'] : json_encode($file['secondValue']);

    return match ($valueType) {
        SAME_VALUE => "    $key: $firstValue",
        DIFFERENT_VALUE => "  - $key: $firstValue\n  + $key: $secondValue",
        SECOND_VALUES_NOT_EXIST => "  - $key: $firstValue",
        FIRST_VALUE_NOT_EXIST => "  + $key: $secondValue"
    };
}

function getValidateFileContent(string ...$files): array|\ErrorException
{
    $content = [];
    foreach ($files as $file) {
        if (!file_exists($file)) {
            throw new \ErrorException("file not found");
        }
        $content[] = json_decode(file_get_contents($file), true);
    }
    return $content;
}

/**
 * @param array<int, string> $keys
 * @param array<mixed, mixed> $firstFile
 * @param array<mixed, mixed> $secondFile
 */

function getStructureByKeys(array $keys, array $firstFile, array $secondFile): array
{
    return array_map(function ($key) use ($firstFile, $secondFile) {
        $firstFileValue = array_key_exists($key, $firstFile) ? $firstFile[$key] : null;
        $secondFileValue = array_key_exists($key, $secondFile) ? $secondFile[$key] : null;
        return [
            'key' => $key,
            'firstValue' => $firstFileValue,
            'secondValue' => $secondFileValue,
            'valueType' => getValueType($firstFileValue, $secondFileValue)
        ];
    }, $keys);
}

function getValueType(mixed $firstValue, mixed $secondValue): int
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
