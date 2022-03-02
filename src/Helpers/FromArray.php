<?php

namespace Jaap\HypixelApi\Helpers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jaap\HypixelApi\GameType;
use Jaap\HypixelApi\Hypixel;
use Ramsey\Uuid\Uuid;
use ReflectionProperty;

trait FromArray
{

    /**
     * @param array $array
     * @throws \ReflectionException
     */
    public function fromArray(array $array): void
    {
        foreach ($array as $key => $value) {
            if (property_exists(static::class, $key)) {
                $ref = new ReflectionProperty(static::class, $key);


                switch ($ref->getType()->getName()) {
                    case 'Ramsey\Uuid\UuidInterface':
                        $this->{$key} = Uuid::fromString($value);
                        continue 2;

                    case 'Carbon\CarbonImmutable':
                        if (gettype($value) === 'integer')
                            $this->{$key} = CarbonImmutable::createFromTimestampMsUTC($value);
                        continue 2;

                    case 'Jaap\HypixelApi\GameType':
                        if (gettype($value) === 'string') {
                            $this->{$key} = GameType::getGames(new Hypixel())->where('typeName', '=', $value)->first();
                        }
                        continue 2;

                    case 'Illuminate\Support\Collection':
                        if (gettype($value) === 'array') {
                            $this->{$key} = new Collection($value);
                        }
                        continue 2;

                }


                if ($ref->getType()->getName() === gettype($value)) {
                    $this->{$key} = $value;
                    continue;
                }
            }
            $this->{$key} = $value;
        }
    }
}
