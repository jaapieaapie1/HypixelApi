<?php

namespace Jaap\HypixelApi\Responses;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Models\HypixelModel;
use Ramsey\Uuid\UuidInterface;

class PlayerResponse extends HypixelModel
{
    use FromArray;

    public UuidInterface $uuid;
    public string $displayName;
    public string $rank;
    public string $packageRank;
    public string $newPackageRank;
    public string $monthlyPackageRank;
    public CarbonImmutable $firstLogin;
    public CarbonImmutable $lastLogin;
    public CarbonImmutable $lastLogout;
    public array $stats;
}
