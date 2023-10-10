<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\CakePHP as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class CakePHP implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $path = rtrim($path, '/');
        if (!$fs->fileExists($path . '/bin/cake')) {
            return new EmptyMatchResult();
        }

        $version = $this->detectVersion($fs, $path);

        return new MatchResult($path, $version);
    }

    private function detectVersion(Filesystem $fs, string $path): ?string
    {
        $version = null;

        $versionFile = $path . '/vendor/cakephp/cakephp/VERSION.txt';
        if ($fs->fileExists($versionFile)) {
            $versionData = explode("\n", trim($fs->read($versionFile)));
            $version = trim(array_pop($versionData));
        }

        return $version;
    }
}
