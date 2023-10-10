<?php

namespace Plesk\Wappspector;

use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Throwable;

final class Wappspector
{
    /**
     * @param callable $fsFactory
     */
    public function __construct(private $fsFactory, private array $matchers)
    {
    }

    /**
     * @return MatchResultInterface[]
     * @throws Throwable
     */
    public function run(string $path, string $basePath = '/', int $matchersLimit = 0): iterable
    {
        $fs = ($this->fsFactory)($basePath);

        $result = [];

        /** @var MatcherInterface $matcher */
        foreach ($this->matchers as $matcher) {
            if (($match = $matcher->match($fs, $path)) instanceof EmptyMatchResult) {
                continue;
            }

            $result[] = $match;
            if ($matchersLimit > 0 && count($result) >= $matchersLimit) {
                break;
            }
        }

        return $result;
    }
}
