<?php

namespace Jaap\HypixelApi\Models\Resources\Challenges;

use Jaap\HypixelApi\Helpers\FromArray;
use Jaap\HypixelApi\Helpers\IFromArray;
use Jaap\HypixelApi\Models\HypixelModel;

class Challenge extends HypixelModel implements IFromArray
{
    use FromArray;

    public string $id;
    public string $name;
    public array $rewards;
}
