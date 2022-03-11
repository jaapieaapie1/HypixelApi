<?php

namespace Jaap\HypixelApi\Models\Resources\Achievements;

use Illuminate\Support\Collection;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class GameAchievementData extends HypixelModel
{
    use FromArray;

    /**
     * @var Collection<string, OneTimeAchievement> $oneTime
     */
    public Collection $oneTime;

    public function parseOne_time(array $data) {
        $collection = new Collection();

        foreach ($data as $key => $value) {
            $obj = new OneTimeAchievement();
            $obj->fromArray($value);

            $collection->put($key, $obj);
        }
        $this->oneTime = $collection;
    }

    /**
     * @var Collection<string, TieredAchievement> $tiered
     */
    public Collection $tiered;

    public function parseTiered(array $data) {
        $collection = new Collection();

        foreach ($data as $key => $value) {
            $obj = new TieredAchievement();
            $obj->fromArray($value);

            $collection->put($key, $obj);
        }
        $this->tiered = $collection;
    }
}
