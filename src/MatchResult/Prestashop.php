<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Prestashop extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'prestashop';
    }

    public function getName(): string
    {
        return 'PrestaShop';
    }
}
