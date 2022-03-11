<?php

namespace Jaap\HypixelApi\Models\Resources\Achievements;

use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Helpers\IFromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class AchievementTier extends HypixelModel implements IFromArray
{
    use FromArray;

    public int $tier;
    public int $points;
    public int $amount;
}
