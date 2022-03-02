<?php

namespace Jaap\HypixelApi\Models;

use Jaap\HypixelApi\Helpers\FromArray;

class Session extends HypixelModel
{
    use FromArray;

    public bool $online;
    public ?string $gameType;
    public ?string $mode;
    public ?string $map;
}
