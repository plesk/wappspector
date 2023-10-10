<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Drupal extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'drupal';
    }

    public function getName(): string
    {
        return 'Drupal';
    }
}
