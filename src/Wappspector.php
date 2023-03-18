<?php

namespace Plesk\Wappspector;

use Plesk\Wappspector\WappMatchers\WappMatcherInterface;
use Throwable;

final class Wappspector
{
    public function __construct(private FileSystemFactory $fsFactory, private array $matchers)
    {
    }

    /**
     * @throws Throwable
     */
    public function run(string $path): iterable
    {
        $fs = $this->fsFactory->create($path);

        $result = [];

        /** @var WappMatcherInterface $matcher */
        foreach ($this->matchers as $matcher) {
            $result = [...$result, ...$matcher->match($fs, '/')];
        }

        return $result;
    }
}
