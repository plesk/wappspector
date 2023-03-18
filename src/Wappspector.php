<?php

namespace Plesk\Wappspector;

use Plesk\Wappspector\WappMatchers\WappMatcherInterface;
use Psr\Container\ContainerInterface;
use Throwable;

final class Wappspector
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @throws Throwable
     */
    public function run(string $path): iterable
    {
        $this->container->set('path', $path);
        $matchers = $this->container->get(WappMatcherInterface::class);
        $result = [];

        /** @var WappMatcherInterface $matcher */
        foreach ($matchers as $matcher) {
            $result = [...$result, ...$matcher->match('/')];
        }

        return $result;
    }
}
