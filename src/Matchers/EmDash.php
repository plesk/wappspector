<?php
declare(strict_types=1);
namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmDash as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class EmDash implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $packageJsonPath = rtrim($path, '/') . '/package.json';
        if (!$fs->fileExists($packageJsonPath)) {
            return new EmptyMatchResult();
        }

        $json = json_decode($fs->read($packageJsonPath), true);

        return is_array($json) && (isset($json['emdash']) || isset($json['dependencies']['emdash']))
            ? new MatchResult($path)
            : new EmptyMatchResult();
    }
}
