<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

interface MatcherInterface
{
    /**
     * Checks filesystem by provided path and returns the list of found objects.
     */
    public function match(Filesystem $fs, string $path): MatchResultInterface;
}
