<?php

namespace Jaap\HypixelApi\Models;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\GameType;
use Jaap\HypixelApi\Helpers\FromArray;

class RecentGame extends HypixelModel
{
    use FromArray;

    public CarbonImmutable $date;
    public GameType $gameType;
    public ?string $mode;
    public ?string $map;
    public ?CarbonImmutable $ended;
}
