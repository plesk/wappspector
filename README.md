# wappspector
Command-line interface utility to analyze the file structure of a web hosting server and identify the frameworks and CMS used in the websites hosted on it.

### Technology & Framework

-   PHP
-   Laravel
-   Symfony
-   CodeIgniter
-   CakePHP
-   Yii
-   Composer
-   Ruby
-   Python
-   .NET
-   Node.js

### CMS

-   WordPress
-   Joomla!
-   Drupal
-   PrestaShop
-   TYPO3

## How to build phar

* composer global require clue/phar-composer
* composer install --no-dev
* php -d phar.readonly=off ~/.composer/vendor/bin/phar-composer build .
* ./wappspector.phar

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
$diContainer->set('matchers', [\Plesk\Wappspector\WappMatchers\WordpressMatcher::class]);
```
