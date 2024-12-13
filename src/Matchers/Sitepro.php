<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Sitepro as MatchResult;

class Sitepro implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $rTrimPath = rtrim($path, '/');
        $siteproFolderPath =  $rTrimPath . '/sitepro';
        if (!$fs->directoryExists($siteproFolderPath)) {
            return new EmptyMatchResult();
        }

        return $this->fileContainsString($fs, $rTrimPath . '/web.config', 'sitepro')
               || $this->fileContainsString($fs, $rTrimPath . '/.htaccess', 'sitepro')
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }

    private function fileContainsString(Filesystem $fs, string $filePath, string $searchString): bool
    {
        return $fs->fileExists($filePath) && str_contains($fs->read($filePath), $searchString);
    }
}
