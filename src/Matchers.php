<?php

declare(strict_types=1);


namespace Plesk\Wappspector;

class Matchers
{
    public const COMPOSER = 'composer';
    public const DOTNET = 'dotnet';
    public const DRUPAL = 'drupal';
    public const JOOMLA = 'joomla';
    public const LARAVEL = 'laravel';
    public const PRESTASHOP = 'prestashop';
    public const TYPO3 = 'typo3';
    public const WORDPRESS = 'wordpress';

    public const RUBY = 'ruby';
    public const PHP = 'php';
    public const NODEJS = 'nodejs';
    public const PYTHON = 'python';

    public const UNKNOWN = 'unknown';
    public const NO_HOSTING = 'nohosting';

    private static array $names = [
        self::COMPOSER => 'Composer',
        self::DOTNET => '.NET',
        self::DRUPAL => 'Drupal',
        self::JOOMLA => 'Joomla!',
        self::LARAVEL => 'Laravel',
        self::PRESTASHOP => 'PrestaShop',
        self::TYPO3 => 'TYPO3',
        self::WORDPRESS => 'WordPress',
        self::RUBY => 'Ruby',
        self::PHP => 'PHP',
        self::NODEJS => 'Node.js',
        self::PYTHON => 'Python',
        self::UNKNOWN => 'Unknown',
    ];

    public static function getName(string $matcher): ?string
    {
        return self::$names[$matcher] ?? null;
    }
}
