<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class CodeIgniterMatcher implements WappMatcherInterface
{

    public function match(Filesystem $fs, string $path): iterable
    {
        $path = rtrim($path, '/');
        if (!$fs->fileExists($path . '/spark')) {
            return [];
        }

        $version = $this->detectVersion($fs, $path);

        return [
            'matcher' => Matchers::CODEIGNITER,
            'path' => $path,
            'version' => $version,
        ];
    }

    /**
     * @throws FilesystemException
     */
    private function detectVersion(Filesystem $fs, string $path): ?string
    {

        $versionFile = $path . '/vendor/codeigniter4/framework/system/CodeIgniter.php';
        if (!$fs->fileExists($versionFile)) {
            return null;
        }
        preg_match("/CI_VERSION\\s*=\\s*'([^']+)'/", $fs->read($versionFile), $matches);

        if (count($matches)) {
            return $matches[1];
        }

        return null;
    }
}
