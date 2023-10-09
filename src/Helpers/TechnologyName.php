<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Helpers;

use Plesk\Wappspector\MatchResult;

class TechnologyName
{
    private static array $names = [
        MatchResult\Composer::class => 'Composer',
        MatchResult\DotNet::class => '.NET',
        MatchResult\Drupal::class => 'Drupal',
        MatchResult\Joomla::class => 'Joomla!',
        MatchResult\Laravel::class => 'Laravel',
        MatchResult\Prestashop::class => 'PrestaShop',
        MatchResult\Typo3::class => 'TYPO3',
        MatchResult\Wordpress::class => 'WordPress',
        MatchResult\Ruby::class => 'Ruby',
        MatchResult\Php::class => 'PHP',
        MatchResult\NodeJs::class => 'Node.js',
        MatchResult\Symfony::class => 'Symfony',
        MatchResult\CodeIgniter::class => 'CodeIgniter',
        MatchResult\CakePHP::class => 'CakePHP',
        MatchResult\Yii::class => 'Yii',
        MatchResult\Python::class => 'Python',
        MatchResult\EmptyMatchResult::class => 'Unknown',
    ];

    public static function fromResult(MatchResult\MatchResultInterface $matchResult): ?string
    {
        return self::$names[get_class($matchResult)] ?? self::$names[MatchResult\EmptyMatchResult::class];
    }
}
