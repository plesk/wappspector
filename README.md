# wappspector
Command-line interface utility to analyze the file structure of a web hosting server and identify the frameworks and CMS used in the websites hosted on it.

[![unit-test](https://github.com/plesk/wappspector/actions/workflows/unit-test.yml/badge.svg)](https://github.com/plesk/wappspector/actions/workflows/unit-test.yml)

## Matchers
### Technology & Frameworks

| Technology  | Version    | Check type                       |
|-------------|------------|----------------------------------|
| PHP         | -          | Any `*.php` file                 |
| Ruby        | 2, 3       | `Rakefile` in root dir           |
| Python      | 2, 3       | Any `*.py` file                  |
| Laravel     | 8, 9, 10   | `artisan` file in root dir       |
| Symfony     | 3, 4, 5, 6 | `symfony.lock` file in root dir  |
| CodeIgniter | 4          | `spark` file in root dir         |
| CakePHP     | 3, 4       | `bin/cake` file                  |
| Yii         | 2          | `yii` file in root dir           |
| Composer    | -          | `composer.json` file in root dir |
| .NET        | -          | Any `*.dll` file                 |
| Node.js     | -          | `package.json` file in root dir  |

### CMS
| Name       | Major version           | Check type                                                                                                                                                                           |
|------------|-------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| WordPress  | 2 - 6                   | Existence and contents of `wp-includes/version.php`                                                                                                                                  |
| Joomla!    | 1 - 6                   | Existence and contents of `configuration.php` in root dir and version files in `libraries/src/Version.php` or `libraries/cms/version/version.php` or similar                         |
| Drupal     | 6 - 10                  | Existence and contents of `/modules/system/system.info` or `/core/modules/system/system.info.yml`                                                                                    |
| PrestaShop | 1.6, 1.7.8, 8.0         | Existence and contents of `/config/settings.inc.php`                                                                                                                                 |
| TYPO3      | 7.6, 8.7, 9, 10, 11, 12 | Existence and contents of `/typo3/sysext/core/Classes/Core/SystemEnvironmentBuilder.php` or `/typo3/sysext/core/Classes/Information/Typo3Version.php` or `/t3lib/config_default.php` |
| EmDash     | 0.0.3 - 0.4.0           | Existence and contents of `package.json`                                                                                                                 |

### Site builders
| Name               | Check type                                                                                                                                                                                                                                        |
|--------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Sitejet            | The `index.html` file exists and contains the `ed-element` and `webcard.apiHost=` strings                                                                                                                                                         |
| WebPresenceBuilder | The `index.html` file contains the `<meta name="generator" content="Web Presence Builder <vesrion>">` tag or contains the following DOM structure: the `div` tag with the `page` ID contains the `div` tags with the `watermark` and `layout` IDs |
| Site.pro           | The `sitepro` folder exists and the `sitepro` string is contained in the `web.config` or `.htaccess` files                                                                                                                                        |                                                                                                                                                                                                         
| Duda.co            | The `Style` folder contains the `desktop.css`, `mobile.css`, or `tablet.css` files. The style file contains the `dmDudaonePreviewBody` or `dudaSnipcartProductGalleryId` strings, or the `Scripts/runtime.js` file contains the `duda` string     |
| Siteplus           | The `index.html` file exists and contains the `edit.site` string, and the `/bundle/publish/<version>` directory exists and contains the `bundle.js` file. The `bundle.js` file contains the `siteplus` string                                     |

### Web applications
| Name           | Check type                                                                                                                                                                                                                                       |
|----------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| cPanel Web App | The directory is listed as an application in the account's registry at `~/.cpanel/webapp/registry.json`, that is `~/ea-podman.d/<container>/webapp` for a deployed application or `~/.cpanel/webapp-staging/<name>/source` for a staged one |

Detection does not depend on what the application is built with: the registry is the
authority, so an application is reported whatever it contains, and an empty one is
reported too. The registered application name is returned in the `application` field.

A web application sits three directories below the account home, deeper than the
default `--depth` of 1, so the scan always descends into `ea-podman.d` whatever the
depth limit is. No flag is needed to find one:

```shell
# every web application on the server
./wappspector.phar /home --json | grep -c '"id": "cpanelwebapp"'

# one account
./wappspector.phar /home/someuser
```

Exactly one row is reported per registered application, so the rows can simply be
counted. The registry is the authority on what such a directory is, so the command
reports it as the application it is and drops the lower-priority match for that path:
an application holding an `index.php` is not also reported as PHP. This narrowing
applies to a registered application's own path only -- everywhere else every match is
still reported, the way a Laravel site is also reported as Composer, PHP and JS.

The library reports both and leaves the choice to the caller: `Wappspector::run()`
returns every match in priority order, the web application first, and `--max 1` keeps
the first one.

Only the `webapp` directory inside a container is inspected. `ea-podman.d` and the
container directories inside it are walked through to reach it, but they hold the
containers' own configuration rather than a site, so they are not reported.

The `<container>.bak` directories a redeploy leaves behind are in no registry, so they
are never reported as applications; the scan skips them entirely, along with the copy of
the superseded deploy they still hold, which would otherwise be reported as a live site.

A staged application lives under a dot-directory, which the CLI skips while traversing,
so a scan counts deployed applications only; the matcher itself still detects a staged
one when it is given its path directly.

## How to build phar

phar-composer bundles the whole project directory and offers no way to exclude
anything from it, so build from a clean export into a target outside that export:

```shell
composer global require clue/phar-composer

rm -rf build
git archive HEAD --prefix=build/ | tar -x
cd build
composer install --no-dev
php -d phar.readonly=off ~/.composer/vendor/bin/phar-composer build . ../wappspector.phar
cd ..
rm -rf build
```

`.gitattributes` keeps `tests/` and `test-data/` out of the export and `--no-dev`
keeps the development tooling out of `vendor/`, which is the difference between a
2 MB phar and a 200 MB one.

Do not run `phar-composer build .` in the working directory. It has no exclude for
the phar it is about to overwrite, so each build bundles the previous one inside
itself and the phar doubles in size every time. Once it outgrows PHP's
`memory_limit` it dies before reaching any code, printing nothing on either stream
and exiting non-zero.

Run the created `wappspector.phar`:
```shell
./wappspector.phar ./test-data
```

## Changing matchers order
To change the matchers order or to disable some of them, you should override `matchers` entry of DI container.

```php
$diContainer = \Plesk\Wappspector\DIContainer::build();
$matchers = $diContainer->get('matchers');
array_unshift($matchers, \Some\New\Matcher::class);
$diContainer->set('matchers', $matchers);
```

or

```php
// only detect WordPress installs
$diContainer = \Plesk\Wappspector\DIContainer::build();
$diContainer->set('matchers', [\Plesk\Wappspector\Matchers\Wordpress::class]);
```

## Testing
```shell
./vendor/bin/phpunit
```
