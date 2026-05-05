<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use JsonException;
use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\NodeJs as MatchResult;

class NodeJs implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $basePath = rtrim($path, '/');
        $packageJsonPath = $basePath . '/package.json';
        if ($fs->fileExists($packageJsonPath)) {
            $json = [];
            try {
                $json = json_decode($fs->read($packageJsonPath), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                // ignore package.json errors
            }
            return new MatchResult($path, null, $json['name'] ?? null);
        }

        $filesToCheck = ['server.js', 'app.js', 'index.js'];
        foreach ($filesToCheck as $file) {
            if ($fs->fileExists($basePath . '/' . $file)) {
                return new MatchResult($path, null, null);
            }
        }
        return new EmptyMatchResult();
    }
}
