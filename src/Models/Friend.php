<?php

namespace Jaap\HypixelApi\Models;

use Carbon\CarbonImmutable;
use Jaap\HypixelApi\Helpers\FromArray;
use Ramsey\Uuid\UuidInterface;

class Friend extends HypixelModel
{
    use FromArray;

    public UuidInterface $uuidSender;
    public UuidInterface $uuidReceiver;
    public CarbonImmutable $started;
}
