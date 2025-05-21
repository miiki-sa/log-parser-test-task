<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Parser\LinesIterator;

use App\Domain\Log\Parser\LinesIterator\LinesIteratorException;
use App\Domain\Log\Parser\LinesIterator\PlainFileIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class PlainFileIteratorTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'log_test_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testIterateValidFile(): void
    {
        $expectedArray = [
            'line1',
            'line2',
            '',
            'line4',
        ];

        file_put_contents($this->tempFile, implode("\n", $expectedArray));

        $iterator = new PlainFileIterator($this->tempFile);
        $lines = iterator_to_array($iterator->lines());

        $this->assertCount(3, $lines);
        $this->assertSame(array_values(array_filter($expectedArray)), $lines);
    }

    public function testIterateFileWithBom(): void
    {
        $expectedArray = [
            'line1',
            'line2',
        ];

        file_put_contents($this->tempFile, "\xEF\xBB\xBF".implode("\n", $expectedArray));

        $iterator = new PlainFileIterator($this->tempFile);
        $lines = iterator_to_array($iterator->lines());

        $this->assertCount(2, $lines);
        $this->assertSame($expectedArray, $lines);
    }

    public function testIterateEmptyFile(): void
    {
        $expectedArray = [];

        file_put_contents($this->tempFile, '');

        $iterator = new PlainFileIterator($this->tempFile);
        $lines = iterator_to_array($iterator->lines());

        $this->assertCount(0, $lines);
        $this->assertSame($expectedArray, $lines);
    }

    public function testIterateNonExistentFile(): void
    {
        $this->expectException(LinesIteratorException::class);
        $this->expectExceptionMessage('File is not exists or not readable non_existent_file.log.');

        new PlainFileIterator('non_existent_file.log');
    }
}
