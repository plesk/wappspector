<?php

namespace Plesk\Wappspector\WappMatchers;

interface WappMatcherInterface
{
    /**
     * Checks filesystem by provided path and returns the list of found objects.
     */
    public function match(string $path): iterable;
}
