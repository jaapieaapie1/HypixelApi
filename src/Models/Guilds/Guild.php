<?php

namespace Jaap\HypixelApi\Models\Guilds;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class Guild extends HypixelModel
{
    use FromArray;

    public string $_id;
    public string $name;
    public string $name_lower;
    public int $coins;
    public int $coinsEver;
    public CarbonImmutable $created;
    /**
     * @var GuildMember[] members
     */
    public array $members;
    /**
     * @var GuildRank[] ranks
     */
    public array $ranks;
    public array $achievements;
    public int $exp;
    public array $guildExpByGameType;
}
