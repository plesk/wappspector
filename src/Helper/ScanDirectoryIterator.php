<?php

declare(strict_types=1);

namespace Plesk\Wappspector\Helper;

use FilesystemIterator;
use Plesk\Wappspector\Matchers\CpanelWebApp;
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
 */
final class ScanDirectoryIterator extends RecursiveIteratorIterator
{
    private const FLAGS = FilesystemIterator::KEY_AS_PATHNAME
        | FilesystemIterator::CURRENT_AS_FILEINFO
        | FilesystemIterator::SKIP_DOTS;

    public function __construct(string $path, private int $maxDepth)
    {
        parent::__construct(new RecursiveDirectoryIterator($path, self::FLAGS), self::SELF_FIRST);
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
}
