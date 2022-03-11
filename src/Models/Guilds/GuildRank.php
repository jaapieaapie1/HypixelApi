<?php

namespace Jaap\HypixelApi\Models\Guilds;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class GuildRank extends HypixelModel implements \Jaap\HypixelApi\Helpers\IFromArray
{
    use FromArray;

    public string $name;
    public bool $default = false;
    public int $priority;
    public CarbonImmutable $created;
}
