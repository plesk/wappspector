<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Helper\InspectorHelper;
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

        $inspectorHelper = new InspectorHelper();

        return $inspectorHelper->fileContainsString($fs, $rTrimPath . '/web.config', 'sitepro')
               || $inspectorHelper->fileContainsString($fs, $rTrimPath . '/.htaccess', 'sitepro')
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }
}
