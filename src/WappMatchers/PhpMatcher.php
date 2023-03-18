<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;

class PhpMatcher implements WappMatcherInterface
{
    public function __construct(private Filesystem $fs)
    {

    }

    public function match(string $path): iterable
    {
        // TODO: Implement match() method.
    }
}
