<?php

namespace Differ\Formatters\Plain;

use Differ\Diff;

const ADDED_VALUE = 'ADDED';
const DELETED_VALUE = 'DELETED';
const UPDATED_VALUE = 'UPDATED';

const COMPLEX_VALUE = '[complex value]';


function getTreeType(array $node): string
{
    return match (Diff\getType($node)) {
        Diff\ADD_VALUE => ADDED_VALUE,
        Diff\DEL_VALUE => DELETED_VALUE,
        Diff\UPDATE_VALUE => UPDATED_VALUE,
        default => throw new \Exception('Error'),
    };
}

/**
 * @param  string  $path
 * @param  string  $type
 * @param  mixed  $firstValue
 * @param  mixed  $secondValue
 * @return array
 */
function getStructTree(string $path, string $type, mixed $firstValue, mixed $secondValue): array
{
    return [
        'path' => $path,
        'type' => $type,
        'firstValue' => $firstValue,
        'secondValue' => $secondValue,
    ];
}

/**
 * @throws \Exception
 */
function getStructDiff(array $diff): string
{
    $tree = makeTree($diff);
    return getFormattedDiffTree($tree);
}

function getFormattedDiffTree(array $tree): string
{
    $content = array_map(function ($node) {
        $path = getPath($node);
        $propertyInfo = "Property '{$path}'";

        $type = getType($node);

        if (ADDED_VALUE === $type) {
            $newValue = parseValue(getScondValue($node));
            return $propertyInfo . " was added with value: {$newValue}";
        } elseif (DELETED_VALUE === $type) {
            return $propertyInfo . " was removed";
        } elseif (UPDATED_VALUE === $type) {
            $oldValue = parseValue(getFirstValue($node));
            $newValue = parseValue(getScondValue($node));
            return $propertyInfo . " was updated. From {$oldValue} to {$newValue}";
        }

        throw new \Exception('Error');
    }, $tree);

    return implode("\n", $content);
}

/**
 * @param  mixed  $value
 * @return string
 */
function parseValue($value): string
{
    if (is_array($value)) {
        return COMPLEX_VALUE;
    }

    if (is_string($value)) {
        return "'$value'";
    }

    return json_encode($value);
}

function makeTree(array $data, string $rootPath = ''): array
{
    return array_reduce($data, function ($acc, $node) use ($rootPath) {
        $key = Diff\getKey($node);

        $path = $rootPath === '' ? $key : "$rootPath.$key";

        if (Diff\hasChildren($node)) {
            return array_merge($acc, makeTree(Diff\getChildren($node), $path));
        }

        if (Diff\SAME_VALUE === Diff\getType($node)) {
            return $acc;
        }

        return [
            ...$acc,
            getStructTree($path, getTreeType($node), getFirstValue($node), getScondValue($node))
        ];
    }, []);
}

function getPath(array $tree): string
{
    return $tree['path'];
}

function getType(array $tree): string
{
    return $tree['type'];
}

function getFirstValue(array $tree)
{
    return $tree['firstValue'];
}

function getScondValue(array $tree)
{
    return $tree['secondValue'];
}