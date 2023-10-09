<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\RubyMatchResult;

class RubyMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    private const RAKEFILE = 'Rakefile';

    /**
     * @throws FilesystemException
     */
    protected function doMatch(Filesystem $fs, string $path): MatchResultInterface
    {
        if (!$fs->fileExists(rtrim($path, '/') . '/' . self::RAKEFILE)) {
            return new EmptyMatchResult();
        }

        return new RubyMatchResult($path);
    }
}
