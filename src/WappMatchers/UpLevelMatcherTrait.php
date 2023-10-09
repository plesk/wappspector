<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

trait UpLevelMatcherTrait
{
    abstract protected function doMatch(Filesystem $fs, string $path): MatchResultInterface;

    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $matcher = $this->safeScanDir($fs, $path);
        if ($matcher instanceof EmptyMatchResult) {
            $matcher = $this->safeScanDir($fs, rtrim($path) . '/../');
        }
        return $matcher;
    }

    private function safeScanDir(Filesystem $fs, string $path): MatchResultInterface
    {
        try {
            $result = $this->doMatch($fs, $path);
        } catch (FilesystemException) {
            // skip dir if it is inaccessible
            $result = new EmptyMatchResult();
        }

        return $result;
    }
}
