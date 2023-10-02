<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\WappMatchers\UpLevelMatcherTrait;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(UpLevelMatcherTrait::class)]
class UpLevelMatcherTraitTest extends TestCase
{
    public const MATCHER = 'markdown';
    protected vfsStreamDirectory $root;
    protected Filesystem $fs;

    private function hasMatcher(iterable $result): void
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('matcher', $result);
        $this->assertEquals($result['matcher'], self::MATCHER);
    }

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
        $this->fs = (new FileSystemFactory())($this->root->url());
    }

    public function getTestMatcher(): WappMatcherInterface
    {
        return new class () implements WappMatcherInterface {
            use UpLevelMatcherTrait;

            protected function doMatch(Filesystem $fs, string $path): array
            {
                $list = $fs->listContents($path);
                foreach ($list as $item) {
                    /** @var StorageAttributes $item */
                    if ($item->isFile() && str_ends_with($item->path(), '.md')) {
                        return [
                            'matcher' => UpLevelMatcherTraitTest::MATCHER,
                            'path' => $path,
                        ];
                    }
                }

                return [];
            }
        };
    }

    public function testNoParentDir(): void
    {
        vfsStream::create([
            'readme.txt' => '',
        ]);

        $result = $this->getTestMatcher()->match($this->fs, '');
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        vfsStream::create([
            'readme.md' => '',
        ]);

        $result = $this->getTestMatcher()->match($this->fs, '');
        $this->hasMatcher($result);
    }

    public function testInaccessibleParentDir(): void
    {
        vfsStream::create([
            'unreadable' => [
                'readable' => [
                    'readme.txt' => '',
                ],
                'readme.md' => '',
            ],
        ]);

        $this->root->getChild('unreadable')->chmod(0000)->chown(0);

        $result = $this->getTestMatcher()->match($this->fs, 'unreadable/readable');
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        $this->root->getChild('unreadable')->chmod(0777);
        $result = $this->getTestMatcher()->match($this->fs, 'unreadable/readable');
        $this->hasMatcher($result);
    }

    public function testParentDirNormalization(): void
    {
        vfsStream::create([
            'parent' => [
                'child' => [
                    'readme.txt' => '',
                ],
                'readme.md' => '',
            ],
        ]);

        $result = $this->getTestMatcher()->match($this->fs, 'parent/child');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertStringNotContainsString('..', $result['path']);
    }

    public function testEmptyDir(): void
    {
        vfsStream::create([
            'parent' => [
                'child' => [
                ],
            ],
        ]);

        $result = $this->getTestMatcher()->match($this->fs, 'parent/child');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testNonexistentDir(): void
    {
        $result = $this->getTestMatcher()->match($this->fs, 'no-parent-dir/no-child-dir');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
