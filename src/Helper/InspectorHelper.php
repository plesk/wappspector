<?php
// Copyright 1999-2024. WebPros International GmbH. All rights reserved.
declare(strict_types=1);

namespace Plesk\Wappspector\Helper;

use League\Flysystem\Filesystem;

class InspectorHelper
{
    public function fileContentContainsString(string $fileContent, string $searchString): bool
    {
        return str_contains($fileContent, $searchString);
    }

    public function fileContentMatchesString(string $fileContent, string $searchPattern): bool
    {
        return preg_match($searchPattern, $fileContent) === 1;
    }

    public function fileContainsString(Filesystem $fs, string $filePath, string $searchString): bool
    {
        return $fs->fileExists($filePath) && str_contains($fs->read($filePath), $searchString);
    }
}
