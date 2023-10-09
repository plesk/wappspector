<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

interface WappMatcherInterface
{
    /**
     * Checks filesystem by provided path and returns the list of found objects.
     */
    public function match(Filesystem $fs, string $path): MatchResultInterface;
}
