<?php

namespace Jaap\HypixelApi;

use Illuminate\Database\Eloquent\Collection;
use JetBrains\PhpStorm\Pure;

class GameType
{

    public static Collection $GAMES;

    public int $id;
    public string $name;
    public string $typeName;
    public string $cleanName;
    /**
     * @var GameMode[]
     */
    public array $modes;
    public bool $retired;

    /**
     * @param int $id
     * @param string $name
     * @param string $typeName
     * @param GameMode[] $modes
     * @param string|null $cleanName
     */
    public function __construct(int $id, string $typeName, string $name, array $modes, string $cleanName = null, bool $retired = false) {
        $this->id = $id;
        $this->name = $name;
        $this->typeName = $typeName;
        $this->modes = $modes;
        $this->cleanName = $cleanName ?? $name;
        $this->retired = $retired;
    }

    public static function getGames(Hypixel $hypixel): Collection
    {
        if (!isset(self::$GAMES)) {
            self::$GAMES = $hypixel->requestAllGames();
        }
        return self::$GAMES;
    }

    public function getWithoutModes(): GameType {
        $clone = clone $this;

        unset($clone->modes);
        return $clone;
    }
}
