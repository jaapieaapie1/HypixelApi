<?php

namespace Jaap\HypixelApi\Responses;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class KeyInformationResponse
{
    public readonly string $key;
    public readonly UuidInterface $owner;
    public readonly int $limit;
    public readonly int $queriesInPasMin;
    public readonly int $totalQueries;

    public function __construct(string $key, UuidInterface $owner, int $limit, int $queriesInPasMin, int $totalQueries) {
        $this->key = $key;
        $this->owner = $owner;
        $this->limit = $limit;
        $this->queriesInPasMin = $queriesInPasMin;
        $this->totalQueries = $totalQueries;
    }
}
