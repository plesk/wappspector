<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Ruby as MatchResult;

class Ruby implements MatcherInterface
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

        return new MatchResult($path);
    }
}
