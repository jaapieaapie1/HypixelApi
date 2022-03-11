<?php

namespace Jaap\HypixelApi;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Jaap\HypixelApi\Events\InvalidApiKeyEvent;
use Jaap\HypixelApi\Exceptions\RateLimitException;
use Jaap\HypixelApi\Models\Resources\Achievements\Achievements;
use Jaap\HypixelApi\Models\Friend;
use Jaap\HypixelApi\Models\Guilds\Guild;
use Jaap\HypixelApi\Models\RankedSkywarsStats;
use Jaap\HypixelApi\Models\RecentGame;
use Jaap\HypixelApi\Models\Resources\Challenges\Challenges;
use Jaap\HypixelApi\Models\Session;
use Jaap\HypixelApi\Responses\KeyInformationResponse;
use Jaap\HypixelApi\Responses\PlayerResponse;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Hypixel
{

    private string $API_KEY;
    private UuidInterface $playersUuid;

    /**
     * @param string|null $API_KEY
     */
    public function __construct(string $API_KEY = null) {
        if ($API_KEY === null)
            $API_KEY = config('hypixel.API_KEY');

        $this->API_KEY = $API_KEY;
    }

    /**
     * Get the API key
     *
     * @return string API key
     */
    public function getApiKey(): string {
        return $this->API_KEY;
    }

    /**
     * Get base url
     *
     * @return string base url
     */
    public function getBaseUrl(): string {
        return config('hypixel.BASE_URL');
    }

    /**
     * Request all games from hypixel
     *
     * @return Collection
     */
    public function requestAllGames(): Collection {
        return Cache::get('hypixel.resources.games', function () {
            $data = $this->makeRequest("/resources/games");
            $games = $data["games"];

            $gamesCollection = new Collection();

            foreach ($games as $typeName => $game) {
                $modes = [];

                if (key_exists('modeNames', $game)) {
                    foreach ($game['modeNames'] as $name => $displayName) {
                        $modes[] = new GameMode($name, $displayName);
                    }
                }

                $gamesCollection->add(new GameType(
                    $game['id'],
                    $typeName,
                    $game['databaseName'],
                    $modes,
                    $game['name'],
                    array_key_exists('retired', $game) ? $game['retired'] : false,
                ));
            }

            Cache::put('hypixel.resources.games', $gamesCollection, ttl: 900);
            return $gamesCollection;
        });
    }

    /**
     * Get key information of the configured key
     *
     * @param bool $withoutCache
     * @return KeyInformationResponse
     * @throws RateLimitException
     */
    public function getKeyInformation(bool $withoutCache = false): KeyInformationResponse {
        if ($withoutCache) {
            $keyResponse = $this->requestKeyInformation();
            Cache::put('hypixel.key.' . $this->getApiKey(), $keyResponse, ttl: 900);
            return $keyResponse;
        }
        return Cache::get('hypixel.key', function () {
            $keyResponse = $this->requestKeyInformation();
            Cache::put('hypixel.key', $keyResponse, ttl: 900);
            return $keyResponse;
        });
    }

    /**
     * Get key information
     *
     * @return KeyInformationResponse
     * @throws RateLimitException
     */
    private function requestKeyInformation(): KeyInformationResponse {
        $data = $this->makeRequest('/key');
        $record = $data["record"];

        return new KeyInformationResponse(
            $record["key"],
            Uuid::fromString($record["owner"]),
            $record["limit"],
            $record["queriesInPastMin"],
            $record["totalQueries"],
        );
    }

    /**
     * Get the current players uuid
     *
     * @param bool $withoutCache
     * @return UuidInterface
     */
    public function getPlayersUuid(bool $withoutCache = false): UuidInterface
    {
        if (!isset($this->playersUuid)) {
            $this->playersUuid = $this->getKeyInformation($withoutCache)->owner;
        }
        return $this->playersUuid;
    }

    /**
     * Get a player from the Hypixel API
     *
     * @param UuidInterface|null $uuid the players  uuid
     * @return PlayerResponse
     * @throws Exception
     */
    public function getPlayer(UuidInterface $uuid = null): PlayerResponse
    {
        $this->orDefaultUuid($uuid);

        return Cache::get('hypixel.player.' . $uuid->serialize(), function () use ($uuid) {
            $data = $this->makeRequest('/player', ['uuid' => $uuid->serialize()]);
            $player = $data['player'];

            $response = new PlayerResponse();
            $response->fromArray($player);

            Cache::put('hypixel.player.' . $uuid->serialize(), $response, ttl: 120);
            return $response;
        });
    }

    /**
     * Get friends of a player.
     *
     * @param UuidInterface|null $uuid
     * @return Friend[]
     * @throws Exception
     */
    public function getFriends(UuidInterface $uuid = null): array {
        $this->orDefaultUuid($uuid);

        return Cache::get('hypixel.friends.' . $uuid->serialize(), function () use ($uuid) {
             $data = $this->makeRequest('/friends', ['uuid' => $uuid->serialize()]);
             $records = $data['records'];

             $friends = [];
             foreach ($records as $record) {
                 $friend = new Friend();
                 $friend->fromArray($record);
                 $friends[] = $friend;
             }
             Cache::put('hypixel.friends.' . $uuid->serialize(), $friends, ttl: 900);
             return $friends;
        });
    }

    /**
     * Get recent games of a player.
     *
     * @param UuidInterface|null $uuid
     * @return RecentGame[]
     * @throws Exception
     */
    public function getRecentGames(UuidInterface $uuid = null): array {
        $this->orDefaultUuid($uuid);

        return Cache::get('hypixel.recentGames.' . $uuid->serialize(), function () use ($uuid) {
            $data = $this->makeRequest('/recentgames', ['uuid' => $uuid->serialize()]);
            $records = $data['games'];

            $recentGames = [];
            foreach ($records as $record) {
                $recentGame = new RecentGame();
                $recentGame->fromArray($record);
                $recentGames[] = $recentGame;
            }
            Cache::put('hypixel.recentGames.' . $uuid->serialize(), $recentGames, ttl: 120);
            return $recentGames;
        });
    }

    /**
     * Get a player's status
     *
     * @param UuidInterface|null $uuid
     * @return Session|null
     * @throws Exception
     */
    public function getStatus(UuidInterface $uuid = null): ?Session {
        $this->orDefaultUuid($uuid);

        return Cache::get('hypixel.status.' . $uuid->serialize(), function () use ($uuid) {
            $data = $this->makeRequest('/status', ['uuid' => $uuid->serialize()]);
            $sessionArray = $data['session'];

            $session = new Session();
            $session->fromArray($sessionArray);
            Cache::put('hypixel.status.' . $uuid->serialize(), $session, ttl: 30);
            return $session;
        });
    }

    /**
     * Get guild
     *
     * @param string|null $id
     * @param UuidInterface|null $uuid
     * @param string|null $name
     * @return Guild
     * @throws Exception
     */
    public function getGuild(string $id = null, UuidInterface $uuid = null, string $name = null): Guild {
        if (
            $id === null &&
            $uuid === null &&
            $name === null
        )
            $uuid = $this->getPlayersUuid();

        $query = [];
        if ($id !== null)
            $query['id'] = $id;
        if ($uuid !== null)
            $query['player'] = $uuid->serialize();
        if ($name !== null)
            $query['name'] = $name;

        return Cache::get('hypixel.guild.' . json_encode($query), function () use ($query) {
            $data = $this->makeRequest('/guild', $query);
            $guildData = $data['guild'];

            $guild = new Guild();
            $guild->fromArray($guildData);
            Cache::put('hypixel.guild.' . json_encode($query), $guild, ttl: 180);
            return $guild;
        });
    }

    /**
     * Get ranked skywars stats
     *
     * @param UuidInterface $uuid
     * @return RankedSkywarsStats|null
     * @throws Exception
     */
    public function getRankedSkywarsStats(UuidInterface $uuid): ?RankedSkywarsStats {
        $this->orDefaultUuid($uuid);

        return Cache::get('hypixel.skywars.ranked.' . $uuid->serialize(), function () use ($uuid) {
            $data = $this->makeRequest('/player/ranked/skywars', ['uuid' => $uuid]);

            if ($data == null)
                return null;

            $data = $data['result'];

            $ranked = new RankedSkywarsStats();
            $ranked->fromArray($data);
            Cache::put('hypixel.skywars.ranked.' . $uuid->serialize(), $ranked, ttl: 120);
            return $ranked;
        });
    }

    /**
     * @return Achievements|null
     */
    public function getAchievements(): ?Achievements {
        return Cache::get('hypixel.achievements', function () {
            $data = $this->makeRequest('/resources/achievements');

            if ($data === null)
                return null;

            $data = $data['achievements'];
            $achievements = new Achievements();
            $achievements->parseGames($data);

            Cache::put('hypixel.achievements', $achievements, 3600);
            return $achievements;
        });
    }

    public function getChallenges(): ?Challenges {
        return Cache::get('hypixel.challenges', function () {
            $data = $this->makeRequest('/resources/challenges');

            if ($data === null)
                return null;

            $data = $data['challenges'];
            $challenges = new Challenges();
            $challenges->parseGames($data);

            Cache::put('hypixel.challenges', $challenges, 3600);
            return $challenges;
        });
    }

    private function orDefaultUuid(?UuidInterface &$uuid) {
        if ($uuid === null)
            $uuid = $this->getPlayersUuid();
    }

    /**
     * @throws RateLimitException
     */
    private function makeRequest(string $path, array $query = []): ?array {
        if (RateLimiter::remaining('hypixel:request:' . $this->getApiKey(), $perMinute = 120)) {
            RateLimiter::hit('hypixel:request:' . $this->getApiKey());

            $uri = Uri::withQueryValues(new Uri($this->getBaseUrl() . $path), $query);
            $response = Http::withHeaders([
                'API-Key' => $this->API_KEY
            ])->get($uri);

            if ($response->status() === 403) {
                $event = new InvalidApiKeyEvent($this);
                Event::dispatch($event);
            }

            if ($response->status() !== 200) {
                return null;
            }

            echo json_encode($response->json());

            return $response->json();
        }

        throw new RateLimitException('Hypixel rate limit reached');
    }
}
