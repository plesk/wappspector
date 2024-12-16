<?php
// Copyright 1999-2024. WebPros International GmbH. All rights reserved.
declare(strict_types=1);

namespace Test\Helper;

use League\Flysystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\Helper\InspectorHelper;

#[CoversClass(InspectorHelper::class)]
class InspectorHelperTest extends TestCase
{
    private const INDEX_HTML_FILE = 'index.html';
    private InspectorHelper $subjectUnderTest;
    private Filesystem $fileSystem;

    protected function setUp(): void
    {
        $this->subjectUnderTest = new InspectorHelper();
        $this->fileSystem = (new FileSystemFactory())(TESTS_DIR . '/../test-data');
    }

    #[DataProvider('fileContentContainsStringProvider')]
    public function testFileContentContainsString(
        string $searchString,
        bool $expectedResult,
    ): void {
        $fileContent = $this->fileSystem->read('inspector-helper/' . self::INDEX_HTML_FILE);
        $this->assertSame($expectedResult, $this->subjectUnderTest->fileContentContainsString($fileContent, $searchString));
    }

    public static function fileContentContainsStringProvider(): array
    {
        return [
            'the string is in the beginning' => ['<!DOCTYPE html>', true],
            'the string is in the middle' => ['"side"', true],
            'no string' => ['something', false],
        ];
    }

    #[DataProvider('fileContentMatchesStringProvider')]
    public function testFileContentMatchesString(
        string $searchPattern,
        bool $expectedResult,
    ): void {
        $fileContent = $this->fileSystem->read('inspector-helper/' . self::INDEX_HTML_FILE);
        $this->assertSame($expectedResult, $this->subjectUnderTest->fileContentMatchesString($fileContent, $searchPattern));
    }

    public static function fileContentMatchesStringProvider(): array
    {
        return [
            'the pattern is in the beginning' => ['/<!DOCTYPE.*>/', true],
            'the pattern is in the middle' => ['/version\:\s"3\.\d\.\d"/', true],
            'no pattern' => ['/something/', false],
        ];
    }

    #[DataProvider('fileContainsStringProvider')]
    public function testFileContainsString(
        string $fileName,
        string $searchString,
        bool $expectedResult,
    ): void {
        $this->assertSame(
            $expectedResult,
            $this->subjectUnderTest->fileContainsString(
                $this->fileSystem,
                'inspector-helper/' . $fileName,
                $searchString,
            ),
        );
    }

    public static function fileContainsStringProvider(): array
    {
        return [
            'file doesn\'t exist' => ['index2.html', '<!DOCTYPE html>', false],
            'file exists and contains the string in the beginning' => [self::INDEX_HTML_FILE, '<!DOCTYPE html>', true],
            'file exists and contains the string in the middle' => [self::INDEX_HTML_FILE, '"side"', true],
            'file exists and doesn\'t contain the string' => [self::INDEX_HTML_FILE, '/something/', false],
        ];
    }
}