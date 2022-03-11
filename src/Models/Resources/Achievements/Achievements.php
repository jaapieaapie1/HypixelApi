<?php

namespace Jaap\HypixelApi\Models\Resources\Achievements;

use Illuminate\Support\Collection;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Helpers\IFromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class Achievements extends HypixelModel implements IFromArray
{
    use FromArray;

    /**
     * @var Collection<string, GameAchievementData> $games
     */
    public Collection $games;

    public function parseGames(array $data) {
        $collection = new Collection();

        foreach ($data as $key => $value) {
            $obj = new GameAchievementData();
            $obj->fromArray($value);
            $collection->put($key, $obj);
        }

        $this->games = $collection;
    }
}
