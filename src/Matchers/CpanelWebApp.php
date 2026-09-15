<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use JsonException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\CpanelWebApp as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

/**
 * Detects a web application deployed by the cPanel Web App feature.
 *
 * Such an application is not identified by a marker file in its own directory, and what
 * it is built with is beside the point: the account owns a registry of them, and each
 * record says where its directory is. So the matcher walks up from the inspected
 * directory to the home directory holding the registry, then checks whether the
 * inspected directory is the directory of one of the registered applications.
 *
 * Exactly one directory per registered application matches, so a recursive scan reports
 * each application once and the results can simply be counted.
 */
class CpanelWebApp implements ExclusiveMatcherInterface
{
    /**
     * The directory holding an account's containers, one directory each.
     */
    public const CONTAINER_DIR = 'ea-podman.d';

    /**
     * The per-account application registry, relative to the home directory.
     */
    private const REGISTRY_FILE = '.cpanel/webapp/registry.json';

    /**
     * Directory of a deployed application, relative to the home directory. The
     * placeholder is the name of the podman container that hosts it.
     */
    private const DEPLOYED_DIR = self::CONTAINER_DIR . '/%s/webapp';

    /**
     * Directory of a staged (not yet deployed) application, relative to the home
     * directory. The placeholder is the application name.
     */
    private const STAGED_DIR = '.cpanel/webapp-staging/%s/source';

    /**
     * How many levels above the inspected directory to look for the registry. An
     * application directory sits three (deployed) or four (staged) levels below the
     * home directory.
     */
    private const MAX_UP_LEVELS = 5;

    /**
     * Everything inside a container belongs to the application deployed there, so this
     * matcher decides what those directories are and no other matcher describes them.
     * Otherwise a scan would report what an application is built with, and would report
     * the `<container>.bak` directories a redeploy leaves behind as live sites.
     */
    public function isExclusiveFor(string $path): bool
    {
        $path = '/' . trim(str_replace('\\', '/', $path), '/') . '/';

        return str_contains($path, '/' . self::CONTAINER_DIR . '/');
    }

    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $parts = array_values(
            array_filter(
                explode('/', str_replace('\\', '/', $path)),
                static fn(string $part): bool => $part !== '' && $part !== '.'
            )
        );

        for ($i = count($parts), $probes = 0; $i >= 0 && $probes <= self::MAX_UP_LEVELS; $i--, $probes++) {
            $home = implode('/', array_slice($parts, 0, $i));
            $registryFile = ($home === '' ? '' : $home . '/') . self::REGISTRY_FILE;

            try {
                if (!$fs->fileExists($registryFile)) {
                    continue;
                }

                // The nearest home directory above the inspected one owns it, so its
                // registry is the only one that can describe it: stop here either way.
                return $this->matchRegisteredApp($fs, $registryFile, $path, array_slice($parts, $i));
            } catch (FilesystemException) {
                // skip dir if it is inaccessible
                return new EmptyMatchResult();
            }
        }

        return new EmptyMatchResult();
    }

    /**
     * @param string[] $relativeParts Inspected path, relative to the home directory
     * @throws FilesystemException
     */
    private function matchRegisteredApp(
        Filesystem $fs,
        string $registryFile,
        string $path,
        array $relativeParts
    ): MatchResultInterface {
        // The home directory itself is not an application, only a host for them.
        if ($relativeParts === []) {
            return new EmptyMatchResult();
        }

        try {
            $registry = json_decode($fs->read($registryFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            // ignore registry.json errors
            return new EmptyMatchResult();
        }

        if (!is_array($registry) || !is_array($registry['apps'] ?? null)) {
            return new EmptyMatchResult();
        }

        $relative = implode('/', $relativeParts);

        foreach ($registry['apps'] as $app) {
            if (is_array($app) && $this->appDir($app) === $relative) {
                return new MatchResult($path, null, $this->stringValue($app, 'name'));
            }
        }

        return new EmptyMatchResult();
    }

    /**
     * The one home-relative directory that identifies the application. A record that
     * names the container it was deployed into is deployed; one that does not is still
     * staged.
     */
    private function appDir(array $app): ?string
    {
        if (($container = $this->stringValue($app, '_container_name')) !== null) {
            return sprintf(self::DEPLOYED_DIR, $container);
        }

        if (($name = $this->stringValue($app, 'name')) !== null) {
            return sprintf(self::STAGED_DIR, $name);
        }

        return null;
    }

    private function stringValue(array $app, string $key): ?string
    {
        $value = $app[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }
}
