<?php


namespace Differ\Formatters\Json;

function getDiff(array $tree): string
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}