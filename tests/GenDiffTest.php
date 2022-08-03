<?php

namespace GenDiffTest;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private function getFixturesForPlainTest(): array
    {
        return [
            'json' => [
                'firstFile' => __DIR__ . '/fixtures/file1.json',
                'secondFile' => __DIR__ . '/fixtures/file2.json',
                'expectedStructure' => __DIR__ . '/fixtures/expected.json',
                'format' => 'json'
            ],
            'yaml' => [
                'firstFile' => __DIR__ . '/fixtures/file1.yml',
                'secondFile' => __DIR__ . '/fixtures/file2.yml',
                'expectedStructure' => __DIR__ . '/fixtures/expected.yml',
                'format' => 'yml'
            ]
        ];
    }

    /**
     * @dataProvider  getFixturesForPlainTest
     */
    public function testAssertSameResult($firstFile, $secondFile, $expectedStructure, $format): void
    {
        $this->assertStringEqualsFile($expectedStructure, genDiff($firstFile, $secondFile, $format));
    }
}
