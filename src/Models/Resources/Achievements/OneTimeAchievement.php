<?php

namespace Jaap\HypixelApi\Models\Resources\Achievements;

use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Helpers\IFromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class OneTimeAchievement extends HypixelModel implements IFromArray
{
    use FromArray;

    public int $points;
    public string $name;
    public string $description;
    public float $gamePercentUnlocked;
    public float $globalPercentUnlocked;
}
