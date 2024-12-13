<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Sitejet as MatchResult;

class Sitejet implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $indexHtmlPath = rtrim($path, '/') . '/index.html';
        if (!$fs->fileExists($indexHtmlPath)) {
            return new EmptyMatchResult();
        }

        $fileContent = $fs->read($indexHtmlPath);

        return $this->fileContentContainsString($fileContent, 'ed-element')
               && $this->fileContentContainsString($fileContent, 'webcard.apiHost=')
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }

    private function fileContentContainsString(string $fileContent, string $searchString): bool
    {
        return str_contains($fileContent, $searchString);
    }
}
