<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use JsonException;
use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\NodeJsMatchResult;

class NodeJsMatcher implements WappMatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $packageFile = rtrim($path, '/') . '/package.json';

        if (!$fs->fileExists($packageFile)) {
            return new EmptyMatchResult();
        }

        $json = [];
        try {
            $json = json_decode($fs->read($packageFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // ignore package.json errors
        }

        return new NodeJsMatchResult($path, null, $json['name'] ?? null);
    }
}
