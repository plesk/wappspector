<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\CodeIgniter as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class CodeIgniter implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $path = rtrim($path, '/');
        if (!$fs->fileExists($path . '/spark')) {
            return new EmptyMatchResult();
        }

        return new MatchResult($path, $this->detectVersion($fs, $path));
    }

    /**
     * @throws FilesystemException
     */
    private function detectVersion(Filesystem $fs, string $path): ?string
    {
        $versionFile = $path . '/vendor/codeigniter4/framework/system/CodeIgniter.php';
        if (!$fs->fileExists($versionFile)) {
            return null;
        }
        preg_match("/CI_VERSION\\s*=\\s*'([^']+)'/", $fs->read($versionFile), $matches);

        if ($matches !== []) {
            return $matches[1];
        }

        return null;
    }
}
