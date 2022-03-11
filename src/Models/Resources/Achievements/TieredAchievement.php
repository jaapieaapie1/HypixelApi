<?php

namespace Jaap\HypixelApi\Models\Resources\Achievements;

use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Helpers\IFromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class TieredAchievement extends HypixelModel implements IFromArray
{
    use FromArray;

    public string $name;
    public string $description;
    /**
     * @var array<AchievementTier> $tiers
     */
    public array $tiers = [];

    public function parseTiers(array $data) {
        $this->parseArray(AchievementTier::class, $data, $this->tiers);
    }
}
