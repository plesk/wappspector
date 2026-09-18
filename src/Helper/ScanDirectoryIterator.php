<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Helper;

use FilesystemIterator;
use Plesk\Wappspector\Matchers\CpanelWebApp;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Walks the directories under a path, depth-limited, for inspection.
 *
 * The depth limit is relaxed for one directory: a web application registered by the
 * cPanel Web App feature lives in `ea-podman.d/<container>/webapp`, three levels below
 * the account home and so deeper than any useful default. Pruning at the limit would
 * hide every web application on the server unless the caller knew to raise `--depth`,
 * so the two levels under `ea-podman.d` are always descended into. They hold one
 * directory per container and are cheap to walk; nothing below them is, and nothing
 * below them is descended into either.
 *
 * One directory is left out of the walk entirely: `ea-podman.d/<container>.bak`, the
 * copy a redeploy leaves behind.
 */
final class ScanDirectoryIterator extends RecursiveIteratorIterator
{
    /**
     * Suffix of the directory a redeploy leaves behind, alongside the container it
     * replaced.
     */
    private const LEFT_OVER_SUFFIX = '.bak';

    private const FLAGS = FilesystemIterator::KEY_AS_PATHNAME
        | FilesystemIterator::CURRENT_AS_FILEINFO
        | FilesystemIterator::SKIP_DOTS;

    public function __construct(string $path, private int $maxDepth)
    {
        parent::__construct(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($path, self::FLAGS),
                static fn(SplFileInfo $item): bool => !self::isLeftOverContainer($item)
            ),
            self::SELF_FIRST
        );
    }

    public function callHasChildren(): bool
    {
        if (!parent::callHasChildren()) {
            return false;
        }

        return $this->getDepth() < $this->maxDepth || $this->holdsContainers();
    }

    /**
     * Whether the current directory is `ea-podman.d` itself, or one of the container
     * directories directly inside it.
     */
    private function holdsContainers(): bool
    {
        $current = $this->current();

        if (!$current instanceof SplFileInfo) {
            return false;
        }

        return $current->getFilename() === CpanelWebApp::CONTAINER_DIR
            || $current->getPathInfo()?->getFilename() === CpanelWebApp::CONTAINER_DIR;
    }

    /**
     * Whether the path is a container's plumbing rather than a place an application
     * lives: `ea-podman.d` itself, one of the container directories inside it, or
     * anything in a container other than its `webapp` directory.
     *
     * The walk passes through these to reach the application directories below them,
     * but none of them is a document root. Inspecting one reports a container's own
     * configuration as though it were a site -- an `index.php` next to the container
     * config reads as a PHP site, and the container is listed alongside the
     * application it hosts.
     */
    public static function isContainerInternals(string $path): bool
    {
        $parts = explode('/', str_replace('\\', '/', $path));
        $containerDir = array_search(CpanelWebApp::CONTAINER_DIR, $parts, true);

        if ($containerDir === false) {
            return false;
        }

        $below = array_slice($parts, $containerDir + 1);

        return count($below) !== 2 || $below[1] !== CpanelWebApp::APP_DIR;
    }

    /**
     * Whether the item is the container copy a redeploy leaves behind. Such a copy is
     * in no registry, so it is not a web application, yet it still holds a full copy of
     * the superseded one — reporting what is inside it would report every superseded
     * deploy on the server as a live site. It is dropped from the walk, along with
     * everything below it.
     *
     * This exclusion is not a caller preference. Should an `--ignore=<pattern>` option
     * ever be added, this one must stay in effect regardless of what the caller passes.
     */
    private static function isLeftOverContainer(SplFileInfo $item): bool
    {
        return $item->isDir()
            && str_ends_with($item->getFilename(), self::LEFT_OVER_SUFFIX)
            && $item->getPathInfo()?->getFilename() === CpanelWebApp::CONTAINER_DIR;
    }
}
