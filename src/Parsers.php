<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function getFileDecode(string $firstFile, string $secondFile, $format): array
{
    return match ($format) {
        "json" => [
            0 => json_decode($firstFile),
            1 => json_decode($secondFile)
        ],
        'yml' => [
            0 => Yaml::parse($firstFile, Yaml::PARSE_OBJECT_FOR_MAP),
            1 => Yaml::parse($secondFile, Yaml::PARSE_OBJECT_FOR_MAP),
        ]
    };
}
