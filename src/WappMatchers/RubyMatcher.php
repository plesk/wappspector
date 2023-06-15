<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class RubyMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    private const RAKEFILE = 'Rakefile';

    /**
     * @throws FilesystemException
     */
    protected function doMatch(Filesystem $fs, string $path): array
    {
        if (!$fs->fileExists(rtrim($path, '/') . '/' . self::RAKEFILE)) {
            return [];
        }

        return [
            'matcher' => Matchers::RUBY,
            'path' => $path,
            'version' => null,
        ];
    }
}
