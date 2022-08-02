<?php

namespace GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testAssertSameResult(): void
    {
        $fileFirst = __DIR__ . '/fixtures/file1.json';
        $fileSecond = __DIR__ . '/fixtures/file2.json';
        $expectedStructure = __DIR__ . '/fixtures/expected.json';

        $this->assertStringEqualsFile($expectedStructure, genDiff($fileFirst, $fileSecond, 'json'));
    }
}
