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
use Plesk\Wappspector\MatchResult\AbstractMatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\WappMatchers\UpLevelMatcherTrait;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(UpLevelMatcherTrait::class)]
class UpLevelMatcherTraitTest extends TestCase
{
    public const MATCHER = 'markdown';
    protected vfsStreamDirectory $root;
    protected Filesystem $fs;

    private function hasMatcher(MatchResultInterface $result): void
    {
        $this->assertInstanceOf(MatchResultInterface::class, $result);
        $this->assertEquals($result->getMatcher(), self::MATCHER);
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

            protected function doMatch(Filesystem $fs, string $path): MatchResultInterface
            {
                $list = $fs->listContents($path);
                foreach ($list as $item) {
                    /** @var StorageAttributes $item */
                    if ($item->isFile() && str_ends_with($item->path(), '.md')) {
                        return new class ($path) extends AbstractMatchResult {
                            public function getMatcher(): string
                            {
                                return 'markdown';
                            }
                        };
                    }
                }

                return new EmptyMatchResult();
            }
        };
    }

    public function testNoParentDir(): void
    {
        vfsStream::create([
            'readme.txt' => '',
        ]);

        $result = $this->getTestMatcher()->match($this->fs, '');
        $this->assertInstanceOf(EmptyMatchResult::class, $result);

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
        $this->assertInstanceOf(EmptyMatchResult::class, $result);

        $this->root->getChild('unreadable')->chmod(0777);
        $result = $this->getTestMatcher()->match($this->fs, 'unreadable/readable');
        $this->hasMatcher($result);
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
        $this->assertInstanceOf(EmptyMatchResult::class, $result);
    }

    public function testNonexistentDir(): void
    {
        $result = $this->getTestMatcher()->match($this->fs, 'no-parent-dir/no-child-dir');
        $this->assertInstanceOf(EmptyMatchResult::class, $result);
    }
}