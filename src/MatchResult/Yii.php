<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Yii extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'yii';
    }

    public function getName(): string
    {
        return 'Yii';
    }
}
