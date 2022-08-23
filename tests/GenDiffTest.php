<?php

namespace GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private function getTestFilesAndFormat(): array
    {
        return [
            'json' => [
                'firstFile' => __DIR__ . '/fixtures/file1.json',
                'secondFile' => __DIR__ . '/fixtures/file2.json',
                'expectedStructure' => __DIR__ . '/fixtures/expected.json',
                'format' => 'stylish'
            ],
            'yaml' => [
                'firstFile' => __DIR__ . '/fixtures/file1.yml',
                'secondFile' => __DIR__ . '/fixtures/file2.yml',
                'expectedStructure' => __DIR__ . '/fixtures/expected.yml',
                'format' => 'stylish'
            ],

            'treeStylish' => [
                'firstFile' => __DIR__ . '/fixtures/stylishFile1.json',
                'secondFile' => __DIR__ . '/fixtures/stylishFile2.json',
                'expectedStructure' => __DIR__ . '/fixtures/stylishExpected',
                'format' => 'stylish'
            ],

            'treePlain' => [
                'firstFile' => __DIR__ . '/fixtures/stylishFile1.json',
                'secondFile' => __DIR__ . '/fixtures/stylishFile2.json',
                'expectedStructure' => __DIR__ . '/fixtures/plainExpected',
                'format' => 'plain'
            ],

            'treeJson' => [
                'firstFile' => __DIR__ . '/fixtures/stylishFile1.json',
                'secondFile' => __DIR__ . '/fixtures/stylishFile2.json',
                'expectedStructure' => __DIR__ . '/fixtures/jsonExpected',
                'format' => 'json'
            ]
        ];
    }

    /**
     * @dataProvider  getTestFilesAndFormat
     */
    public function testAssertSameResult($firstFile, $secondFile, $expectedStructure, $format): void
    {
        $this->assertStringEqualsFile($expectedStructure, genDiff($firstFile, $secondFile, $format));
    }
}
