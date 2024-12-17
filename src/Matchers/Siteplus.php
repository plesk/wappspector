<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Helper\InspectorHelper;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Siteplus as MatchResult;

class Siteplus implements MatcherInterface
{
    private const PUBLISH_DIR_PATH = '/bundle/publish';

    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $rTrimPath = rtrim($path, '/');

        $inspectorHelper = new InspectorHelper();

        if (!$inspectorHelper->fileContainsString($fs, $rTrimPath . '/index.html', 'edit.site')) {
            return new EmptyMatchResult();
        }

        if (!$fs->directoryExists($rTrimPath . self::PUBLISH_DIR_PATH)) {
            return new EmptyMatchResult();
        }

        $publishDirList = $fs->listContents($rTrimPath . self::PUBLISH_DIR_PATH, false);

        // do not check if the item is a directory as on the server when the files a copied the type of the
        // directory is determined as 'file'.
        // By default, there should be just 1 directory in the publish directory
        $versionDirPath = $publishDirList->toArray()[0]['path'] ?? null;

        if ($versionDirPath === null) {
            return new EmptyMatchResult();
        }

        return $inspectorHelper->fileContainsString($fs, $versionDirPath . '/bundle.js', 'siteplus')
            ? new MatchResult($rTrimPath, $this->getSiteplusVersion($versionDirPath))
            : new EmptyMatchResult();
    }

    private function getSiteplusVersion(string $versionDirPath): string
    {
        // get the last part of the path
        $versionDirPathParts = explode('/', $versionDirPath);
        return end($versionDirPathParts);
    }
}
