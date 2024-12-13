<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

use DOMDocument;
use DOMXPath;
use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\WebPresenceBuilder as MatchResult;
use Throwable;

class WebPresenceBuilder implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $indexHtmlPath = rtrim($path, '/') . '/index.html';
        if (!$fs->fileExists($indexHtmlPath)) {
            return new EmptyMatchResult();
        }

        $fileContent = $fs->read($indexHtmlPath);

        return $this->fileContentContainsString(
            $fileContent,
            '/<meta name="generator" content="Web Presence Builder.*">/'
        ) || $this->fileContainsDOMStructure($fileContent)
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }

    private function fileContentContainsString(string $fileContent, string $searchString): bool
    {
        return preg_match($searchString, $fileContent) === 1;
    }

    private function fileContainsDOMStructure(string $fileContent): bool
    {
        $dom = new DOMDocument();
        try {
            libxml_use_internal_errors(true);
            $domIsLoaded = $dom->loadHTML($fileContent);
            libxml_clear_errors();
        } catch (Throwable) {
            return false;
        }

        if ($domIsLoaded === false) {
            return false;
        }

        $xpath = new DOMXPath($dom);

        // Find the <div> with id="page"
        $pageDiv = $xpath->query("//div[@id='page']");

        if ($pageDiv->length === 0) {
            return false;
        }

        $pageNode = $pageDiv->item(0);

        // Check for direct children with the required IDs
        $watermarkDiv = $xpath->query("./div[@id='watermark']", $pageNode);
        $layoutDiv = $xpath->query("./div[@id='layout']", $pageNode);

        return $watermarkDiv->length > 0 && $layoutDiv->length > 0;
    }
}
