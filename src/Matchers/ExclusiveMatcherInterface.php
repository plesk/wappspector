<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Matchers;

/**
 * A matcher that is the sole authority on what the directories it claims are.
 *
 * No other matcher's result is reported for a claimed path, and that holds even when
 * the claiming matcher finds nothing there itself — a claim says "I decide what this
 * is", so if it decides the directory is nothing, the directory is nothing.
 */
interface ExclusiveMatcherInterface extends MatcherInterface
{
    public function isExclusiveFor(string $path): bool;
}
