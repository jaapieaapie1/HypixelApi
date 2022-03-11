<?php

namespace Jaap\HypixelApi\Helpers;

interface IFromArray
{
    /**
     * @param array $array
     * @throws \ReflectionException
     */
    public function fromArray(array $array): void;
}
