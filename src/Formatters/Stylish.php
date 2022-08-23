<?php

namespace Differ\Formatters\Stylish\Formatter;

use Differ\Diff;

use function Differ\Diff\getChildren;
use function Differ\Diff\getKey;

const ADD_VALUE = '+ ';
const DEL_VALUE = '- ';
const SAME_VALUE = '  ';

const AFTER_SPACE_LENGTH = 4;
const BEFORE_SPACE_LENGTH = 2;

function getTreeDiff(array $diff): string
{
    $tree = makeTree($diff);
    $formattedDiff = getFormatDiffTree($tree);
    return rtrim($formattedDiff, PHP_EOL);
}

function cover(array $content, int $deep = 1): string
{
    $start = '{' . PHP_EOL;

    $endIndent = str_repeat(' ', ($deep - 1) * AFTER_SPACE_LENGTH);
    $end = $endIndent . '}' . PHP_EOL;

    return $start . implode('', $content) . $end;
}

/**
 * @param  mixed  $value
 * @param  int  $deep
 * @return string
 */

function valueParser(mixed $value, int $deep): string
{
    if (is_array($value)) {
        $content = array_map(function ($key, $value) use ($deep) {
            $whitespace = str_repeat(' ', $deep * AFTER_SPACE_LENGTH);
            return $whitespace . $key . ': ' . valueParser($value, $deep + 1);
        }, array_keys($value), array_values($value));

        return cover($content, $deep);
    }

    return is_string($value) ? $value . PHP_EOL : json_encode($value) . PHP_EOL;
}

function makeTree(array $data): array
{
    return array_reduce($data, function ($acc, $tree) {
        if (Diff\hasChildren($tree)) {
            $children = makeTree(Diff\getChildren($tree));
            return [
                ...$acc,
                makeTreeStruct(SAME_VALUE, Diff\getKey($tree), Diff\getOldValue($tree), $children),
            ];
        }

        $type = Diff\getType($tree);

        return match ($type) {
            Diff\UPDATE_VALUE => [
                ...$acc,
                makeTreeStruct(DEL_VALUE, Diff\getKey($tree), Diff\getOldValue($tree)),
                makeTreeStruct(ADD_VALUE, Diff\getKey($tree), Diff\getNewValue($tree)),
            ],

            Diff\SAME_VALUE => [
                ...$acc,
                makeTreeStruct(SAME_VALUE, Diff\getKey($tree), Diff\getOldValue($tree)),
            ],

            Diff\ADD_VALUE => [
                ...$acc,
                makeTreeStruct(ADD_VALUE, Diff\getKey($tree), Diff\getNewValue($tree)),
            ],

            Diff\DEL_VALUE => [
                ...$acc,
                makeTreeStruct(DEL_VALUE, Diff\getKey($tree), Diff\getOldValue($tree)),
            ],

            default => $acc
        };
    }, []);
}

function getFormatDiffTree(array $tree, int $deep = 1): string
{
    $struct = array_map(function ($node) use ($deep) {
        $space = str_repeat(' ', $deep * AFTER_SPACE_LENGTH);
        $beforeSpace = substr_replace($space, getSpace($node), -BEFORE_SPACE_LENGTH);

        $keyTmp = $beforeSpace . getKey($node) . ': ';

        $children = getChildren($node);
        if (count($children) > 0) {
            return $keyTmp . getFormatDiffTree($children, $deep + 1);
        }

        $value = valueParser(getValue($node), $deep + 1);

        $key = $value === PHP_EOL ? rtrim($keyTmp, ' ') : $keyTmp;

        return $key . $value;
    }, $tree);

    return cover($struct, $deep);
}

/**
 * @param  string  $space
 * @param  string  $key
 * @param  mixed  $value
 * @param  array  $children
 * @return array
 */
function makeTreeStruct(string $space, string $key, mixed $value, array $children = []): array
{
    return [
        'space' => $space,
        'key' => $key,
        'value' => $value,
        'children' => $children,
    ];
}

function getSpace(array $tree): string
{
    return $tree['space'];
}

function getValue(array $tree): mixed
{
    return $tree['value'];
}
