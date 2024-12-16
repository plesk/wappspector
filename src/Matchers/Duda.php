<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Helper\InspectorHelper;
use Plesk\Wappspector\MatchResult\Duda as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class Duda implements MatcherInterface
{
    private const CSS_FILES = [
        '/Style/desktop.css',
        '/Style/mobile.css',
        '/Style/tablet.css',
    ];

    private const RUNTIME_JS_FILE = '/Scripts/runtime.js';

    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $rTrimPath = rtrim($path, '/');

        $cssFile = $this->getCssFile($fs, $rTrimPath);

        $inspectorHelper = new InspectorHelper();

        if ($cssFile !== null) {
            $cssFileContent = $fs->read($rTrimPath . $cssFile);
            if (
                $inspectorHelper->fileContentContainsString($cssFileContent, 'dmDudaonePreviewBody') ||
                $inspectorHelper->fileContentContainsString($cssFileContent, 'dudaSnipcartProductGalleryId')
            ) {
                return new MatchResult($path);
            }
        }

        if (!$fs->fileExists($rTrimPath . self::RUNTIME_JS_FILE)) {
            return new EmptyMatchResult();
        }

        return $inspectorHelper->fileContainsString($fs, $rTrimPath . self::RUNTIME_JS_FILE, 'duda')
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }

    private function getCssFile(Filesystem $fs, string $path): ?string
    {
        foreach (self::CSS_FILES as $cssFile) {
            if ($fs->fileExists($path . $cssFile)) {
                return $cssFile;
            }
        }

        return null;
    }
}
