<?php

namespace Plesk\Wappspector;

use Plesk\Wappspector\WappMatchers\WappMatcherInterface;
use Throwable;

final class Wappspector
{
    /**
     * @param callable $fsFactory
     * @param array $matchers
     */
    public function __construct(private $fsFactory, private array $matchers)
    {
    }

    /**
     * @throws Throwable
     */
    public function run(string $path): iterable
    {
        $fs = ($this->fsFactory)('/');

        $result = [];

        /** @var WappMatcherInterface $matcher */
        foreach ($this->matchers as $matcher) {
            if (!$match = $matcher->match($fs, $path)) {
                continue;
            }

            $result[] = $match;
        }

        return $result;
    }
}
