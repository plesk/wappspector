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
| Joomla!    | 1 - 4                   | Existence and contents of `configuration.php` in root dir                                                                                                                            |
| Drupal     | 6 - 10                  | Existence and contents of `/modules/system/system.info` or `/core/modules/system/system.info.yml`                                                                                    |
| PrestaShop | 1.6, 1.7.8, 8.0         | Existence and contents of `/config/settings.inc.php`                                                                                                                                 |
| TYPO3      | 7.6, 8.7, 9, 10, 11, 12 | Existence and contents of `/typo3/sysext/core/Classes/Core/SystemEnvironmentBuilder.php` or `/typo3/sysext/core/Classes/Information/Typo3Version.php` or `/t3lib/config_default.php` |

### Site builders
| Name    | Check type                                                                                |
|---------|-------------------------------------------------------------------------------------------|
| Sitejet | The `index.html` file exists and contains the `ed-element` and `webcard.apiHost=` strings |

## How to build phar
```shell
composer global require clue/phar-composer
composer install
php -d phar.readonly=off ~/.composer/vendor/bin/phar-composer build .
```

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
