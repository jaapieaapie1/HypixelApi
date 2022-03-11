<?php

namespace Jaap\HypixelApi\Models\Resources\Challenges;

use Illuminate\Support\Collection;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Helpers\IFromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class Challenges extends HypixelModel implements IFromArray
{
    use FromArray;

    /**
     * @var Collection<string, Collection<int, Challenge>> $games
     */
    public Collection $games;

    public function parseGames(array $data) {
        $gamesCollection = new Collection();

        foreach ($data as $gameKey => $challengeArray) {
            $challengeCollection = new Collection();
            foreach ($challengeArray as $value) {
                $chal = new Challenge();
                $chal->fromArray($value);
                $challengeCollection->add($chal);
            }
            $gamesCollection->put($gameKey, $challengeCollection);
        }

        $this->games = $gamesCollection;
    }
}
