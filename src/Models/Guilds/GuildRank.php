<?php

namespace Jaap\HypixelApi\Models\Guilds;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class GuildRank extends HypixelModel
{
    use FromArray;

    public string $name;
    public bool $default = false;
    public int $priority;
    public CarbonImmutable $created;
}
