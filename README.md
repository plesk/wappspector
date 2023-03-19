# wappspector
Command-line interface utility to analyze the file structure of a web hosting server and identify the frameworks and CMS used in the websites hosted on it.

### Technology & Framework

-   PHP
-   Laravel
-   Composer
-   Ruby
-   Python
-   .NET

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

