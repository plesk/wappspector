<?php

namespace Plesk\Wappspector;

use Plesk\Wappspector\Matchers\ExclusiveMatcherInterface;
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
        foreach ($this->matchersFor($path) as $matcher) {
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

    /**
     * The matchers that may describe `$path`.
     *
     * A matcher can claim sole authority over a path, in which case it is the only one
     * asked about it — see ExclusiveMatcherInterface.
     *
     * @return MatcherInterface[]
     */
    private function matchersFor(string $path): array
    {
        $exclusive = array_filter(
            $this->matchers,
            static fn(MatcherInterface $matcher): bool => $matcher instanceof ExclusiveMatcherInterface
                && $matcher->isExclusiveFor($path)
        );

        return $exclusive === [] ? $this->matchers : $exclusive;
    }
}
