<?php

namespace Jaap\HypixelApi\Models;

use Jaap\HypixelApi\Helpers\IFromArray;

class HypixelModel
{
    public function __construct() {

    }

    /**
     * @param class-string<IFromArray> $fromArrayClass
     * @param array $input
     * @param array|null $list
     * @return void
     * @throws \ReflectionException
     */
    public function parseArray(string $fromArrayClass, array $input, ?array &$list): void {
        $ref = new \ReflectionClass($fromArrayClass);
        foreach ($input as $item) {
            $obj = $ref->newInstance();
            $obj->fromArray($item);
            $list[] = $obj;
        }
    }
}
