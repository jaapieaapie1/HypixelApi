<?php

namespace Jaap\HypixelApi\Models;

use Jaap\HypixelApi\Helpers\FromArray;

class RankedSkywarsStats extends HypixelModel
{
    use FromArray;

    public string $key;
    public int $position;
    public int $score;
}
