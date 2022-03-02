<?php

namespace Jaap\HypixelApi\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Jaap\HypixelApi\Hypixel;
use JetBrains\PhpStorm\Pure;

class InvalidApiKeyEvent
{
    use Dispatchable;

    public string $apiKey;

    #[Pure] public function __construct(Hypixel $hypixel)
    {
        $this->apiKey = $hypixel->getApiKey();
    }

    /**
     * @return bool true if this is equal to the configured API key.
     */
    public function isMainKey(): bool {
        return $this->apiKey == config('hypixel.API_KEY');
    }
}
