<?php

namespace Jaap\HypixelApi\Models\Guilds;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Models\HypixelModel;
use Ramsey\Uuid\UuidInterface;

class GuildMember extends HypixelModel
{
    use FromArray;

    public UuidInterface $uuid;
    public string $rank;
    public CarbonImmutable $joined;
    public int $questParticipation;
    public array $expHistory;
}
