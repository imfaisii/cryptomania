<?php

namespace App\Games\Kernel\ThirdParty\WorldSlotGame;

use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldSlotGame
{
    private static array $highlightedProviders = [
        22,382,79,388,122,54,19,98,359,136,85,133,89,66,26,1,61,22,151,163,375,8,21,51,383,126,103,102,74,58,127,121,23,374,67,155,205,89,132,92
        // 1, 7, 8, 19, 21, 22, 23, 26, 41, 43, 51, 54, 58, 61, 66, 67, 74, 75, 76, 77, 79, 84, 85, 86, 88, 89, 92, 96, 98, 102, 103, 106, 117, 118, 120, 122, 126, 127, 129, 132, 134, 136, 138, 143, 144, 145, 148, 149, 151, 152, 158, 159, 160, 161, 197, 205, 209, 244, 288, 301, 313, 320, 324, 325, 326, 328, 329, 333, 348, 353, 359, 361, 366, 367, 368, 371, 374, 382, 383

    ];

    public static function keys(): array
    {
        return [
            'apiMode' => Settings::get('[WorldSlotGame] API Mode', ''),
            'agentCode' => Settings::get('[WorldSlotGame] Agent Code', ''),
            'agentToken' => Settings::get('[WorldSlotGame] Agent Token', 'cf1255787668e9add736c479bd067e7c'),
            'apiUrl' => Settings::get('[WorldSlotGame] API URL', 'https://fungamess.games/api/v2/cryptomania')
        ];
    }

    public static function debug(): bool
    {
        return Settings::get('[WorldSlotGame] Debug', 'false') === 'true';
    }

    public static function type(): string
    {
        return Settings::get('[WorldSlotGame] Key Type', 'staging');
    }

    public static function request(string $url, array $body = [], string $method = 'get'): array
    {
        if (isset($body['amount'])) {
            $body['amount'] = intval($body['amount']);
        }

        //$body = array_merge($body, ['agent_code' => self::keys()['agentCode'], 'agent_token' => self::keys()['agentToken']]);

        // if (self::debug()) {
        //     Log::info(self::keys());
        // }

        // if (self::debug()) Log::info("Request: " . $url . " " . json_encode($body));

        $client = Http::baseUrl(self::keys()['apiUrl'])->timeout(6000);
        //$client = Http::timeout(10);

        $response = null;

        if (mb_strtolower($method) === 'get') {
            $response = $client->get($url);
        } else if (mb_strtolower($method) === 'post') {
          $response = $client->post($url);
        } else if (mb_strtolower($method) === 'put') {
            $response = $client->put($url);
        } else if (mb_strtolower($method) === 'delete') {
            $response = $client->delete($url);
        }

        $status = $response->status();
        $jsonResponse = $response->body();

        if (self::debug()) {
            // Log::info('Game API response: ' . $status . " " . $jsonResponse);
        }

        $data = json_decode($jsonResponse, true);
        // Log::info($response);
        return [
            'data' => $data?$data:$jsonResponse,
            'status' => $status
        ];
    }

    public function getProviders(): array
    {
        
        if (Cache::has('worldslotgame:loadingGameList'))
            return [];

        if (Cache::has('worldslotgame:providerGameList')) {
            $providers = [];
            $items = Cache::get('worldslotgame:providerGameList');

            $providerIds = [];
            foreach ($items as $item)
                if (!in_array($item['provider']['name'], $providerIds)) $providerIds[] = $item['provider']['name'];

            foreach ($providerIds as $providerID) {
                $games = collect($items)->filter(function ($item) use ($providerID) {
                    return $item['provider']['name'] === $providerID;
                })->toArray();

                $provider = new WorldSlotGameGame($games);
                // if (count($provider->createInstances()) > 0)
                $providers[] = $provider;
                
            }

            return $providers;
        }

        try {
            Cache::put('worldslotgame:loadingGameList', 'true');

            $providerList = self::request('providersList');

            if ($providerList['status'] !== 200 || !isset($providerList['data']) || empty($providerList['data'])) {
                Cache::forget('worldslotgame:loadingGameList');
                return [];
            }

            $providerGames = [];
            $providers = $providerList['data'];
            // var_dump($providers);
            foreach ($providers as $provider) {
                if (!in_array($provider['id'], self::$highlightedProviders)) continue;

                $data = self::request('gameList?provider='.$provider['id']);

                if ($data['status'] !== 200 || !isset($data['data']) && empty($data['data'])) {
                    Cache::forget('worldslotgame:loadingGameList');
                    return [];
                }

                if (isset($data['data']['games'])) {
                    $games = collect($data['data']['games'])->map(function ($game) use ($provider) {
                        $game['provider'] = $provider;
                        return $game;
                    });

                    $providerGames = array_merge($providerGames, $games->toArray());
                }

            }

            $providerGames = collect($providerGames)->filter(function ($providerGame) {
                return in_array($providerGame['provider']['id'], self::$highlightedProviders);
            });

            $providerGames = collect($providerGames)->sortBy(['provider.name', 'name'])->values()->toArray();
            
            Cache::put('worldslotgame:providerGameList', $providerGames, Carbon::now()->addDays(1));
            Cache::forget('worldslotgame:loadingGameList');
            Cache::forget('game:list');
        } catch (\Exception $e) {
            Log::error($e);
            Cache::forget('worldslotgame:loadingGameList');
            return [];
        }

        return $this->getProviders();
    }

    public function getRealProviders(): array
    {
      
        if (Cache::has('worldslotgame:providersList')) {
            $providers = Cache::get('worldslotgame:providersList');

            return $providers;
        }

        try {
            
            $providerList = self::request('providersList');

            if ($providerList['status'] !== 200 || !isset($providerList['data']) || empty($providerList['data'])) return [];

            $providers = collect($providerList['data'])
            ->filter(function ($provider) {
                return in_array($provider['id'], self::$highlightedProviders);
            })
            ->toArray();

            // Log::info('[WorldSlotGame] Providers-----', $providers);

            Cache::put('worldslotgame:providersList', $providers, Carbon::now()->addDays(1));
           
        } catch (\Exception $e) {
            Log::error($e);
            return [];
        }

        return $this->getRealProviders();
    }

    public static function createUser($username)
    {
        $data = self::request('user_create', ['user_code' => $username], 'post')['data'];

        if (!$data['status']) {
            Log::error('[WorldSlotGame] Erro ao realizar o cadastro do usuário', $data);
            throw new \Exception($data['msg']);
        }

        return $data;
    }

    public static function deposit(string $username, $amount)
    {
        $body = ['user_code' => $username, 'amount' => $amount];
        $data = self::request('user_deposit', $body, 'post')['data'];

        if (!$data['status']) {
            Log::error('[WorldSlotGame] Erro ao realizar o depósito', $data);
            throw new \Exception($data['msg']);
        }

        return $data;
    }

    public static function withdraw(string $username, $amount)
    {
        $body = ['method' => 'user_withdraw', 'user_code' => $username, 'amount' => $amount];
        $data = self::request('', $body, 'post')['data'];

        if (!$data['status']) {
            Log::error('[WorldSlotGame] Erro ao realizar a retirada', $data);
            throw new \Exception($data['msg']);
        }

        return $data;
    }

    public function launchGame($gameCode, array $data = [])
    {
        // Dynamically build the request payload based on input data
        $requestData = [
            'gameId' => $gameCode,
            'demo' => 'true', // Assuming demo mode is the default
        ];



        // Make the API request
        $response = self::request('start', $requestData);

        // Ensure the response structure is as expected
        if (!isset($response['data'])) {
            // Define default error message
            $message = 'Unexpected error during game initialization';

            // Handle specific status codes
            switch ($response['status']) {
                case 403:
                    $message = 'The authenticated user is not allowed to access the specified API endpoint.';
                    break;
                case 401:
                    $message = 'Authentication failed.';
                    break;
                default:
                    Log::error('WorldSlotGame error');
                    Log::error($response);
                    break;
            }

            return ['error' => ['message' => $message]];
        }

        // Return the game launch link if successful
        return [
            'id' => '-1', // Consider a meaningful ID if applicable
            'type' => 'third-party',
            'link' => $response['data'] // Adjusted assuming 'data' contains 'link'
        ];
    }
}
