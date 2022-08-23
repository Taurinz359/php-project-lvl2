<?php

namespace Differ\Diff;

use function Functional\sort;

const SAME_VALUE = 1;
const ADD_VALUE = 2;
const DEL_VALUE = 3;
const UPDATE_VALUE = 4;

/**
 * @param string $type
 * @param string $key
 * @param mixed $firstValue
 * @param mixed $secondValue
 * @param array $children
 * @return array
 */
function getStructTree(string $type, string $key, mixed $firstValue, mixed $secondValue, array $children = []): array
{
    return [
        'type' => $type,
        'key' => $key,
        'firstValue' => $firstValue,
        'secondValue' => $secondValue,
        'children' => $children,
    ];
}

/**
 * @param mixed $firstData
 * @param mixed $secondData
 * @return array
 */
function getTree(mixed $firstData, mixed $secondData): array
{
    $mergedKeys = array_keys(array_merge($firstData, $secondData));

    $sortedKeys = sort($mergedKeys, fn($left, $right) => strcmp($left, $right));

    return array_map(
        fn($key) => getStructTreeWithType($key, $firstData, $secondData),
        $sortedKeys,
    );
}

/**
 * @param string $key
 * @param mixed $firstData
 * @param mixed $secondData
 * @return array
 */
function getStructTreeWithType(string $key, mixed $firstData, mixed $secondData): array
{
    if (!array_key_exists($key, $firstData)) {
        return getStructTree(ADD_VALUE, $key, null, $secondData[$key]);
    } elseif (!array_key_exists($key, $secondData)) {
        return getStructTree(DEL_VALUE, $key, $firstData[$key], null);
    } elseif (is_array($secondData[$key]) && is_array($firstData[$key])) {
        $childDiff = getTree($firstData[$key], $secondData[$key]);
        return getStructTree(SAME_VALUE, $key, $firstData[$key], $secondData[$key], $childDiff);
    } elseif ($secondData[$key] === $firstData[$key]) {
        return getStructTree(SAME_VALUE, $key, $firstData[$key], $secondData[$key]);
    }

    return getStructTree(UPDATE_VALUE, $key, $firstData[$key], $secondData[$key]);
}

function getType(array $tree): int
{
    return $tree['type'];
}

function getKey(array $tree): string
{
    return $tree['key'];
}

/**
 * @param array $tree
 * @return mixed
 */
function getOldValue(array $tree): mixed
{
    return $tree['firstValue'];
}

/**
 * @param array $tree
 * @return mixed
 */
function getNewValue(array $tree): mixed
{
    return $tree['secondValue'];
}

function getChildren(array $tree): array
{
    return $tree['children'];
}

function hasChildren(array $tree): bool
{
    return count(getChildren($tree)) > 0;
}
