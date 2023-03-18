<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;

interface WappMatcherInterface
{
    /**
     * Checks filesystem by provided path and returns the list of found objects.
     */
    public function match(Filesystem $fs, string $path): iterable;
}
