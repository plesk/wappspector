<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Helper\InspectorHelper;
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

        $inspectorHelper = new InspectorHelper();

        return $inspectorHelper->fileContentContainsString($fileContent, 'ed-element')
               && $inspectorHelper->fileContentContainsString($fileContent, 'webcard.apiHost=')
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }
}
