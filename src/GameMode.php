<?php

namespace Jaap\HypixelApi;

class GameMode
{
    public readonly string $name;
    public readonly string $displayName;

    public function __construct(string $name, string $displayName) {
        $this->name = $name;
        $this->displayName = $displayName;
    }
}
