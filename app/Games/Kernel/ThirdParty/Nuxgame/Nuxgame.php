<?php

namespace App\Games\Kernel\ThirdParty\Nuxgame;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Nuxgame
{
    private static array $highlightedProviders = [
        'Pragmatic',
        'Quickspin',
        'Thunderkick',
        'Oaks'
    ];

    public static function request(string $method = 'get'): array
    {
        $url = 'https://fungamess.games/api/v2/cryptomania/';

        $client = Http::baseUrl($url)->timeout(100);

        if (mb_strtolower($method) === 'get') {
            $response = $client->get();
        } elseif (mb_strtolower($method) === 'post') {
            $response = $client->post();
        } elseif (mb_strtolower($method) === 'put') {
            $response = $client->put();
        } elseif (mb_strtolower($method) === 'delete') {
            $response = $client->delete();
        }

        $status = $response->status();
        $jsonResponse = $response->body();

        if (self::debug()) {
            Log::info('Nuxgame response: ' . $status . " " . $jsonResponse);
        }

        $responseData = json_decode($jsonResponse, true);

        if (isset($responseData['games']) && is_array($responseData['games'])) {
            return [
                'data' => $responseData['games'],
                'status' => $status
            ];
        } else {
            return [
                'data' => [],
                'status' => $status
            ];
        }
    }

    public static function getProviders(): array
    {
        if (Cache::has('Nuxgame:providersList')) {
            return Cache::get('Nuxgame:providersList');
        }

        try {
            $providers = [];

            $data = self::request('/gameList')['data'];

            if (!isset($data['name'])) {
                // Handle missing data
                return [];
            }

            $items = $data['name'];

            // Assuming there is a maxPage variable
            for ($i = 2; $i <= $maxPage; $i++) {
                $data = self::request('/gameList', ['page' => $i])['data'];
                if (isset($data['name'])) {
                    $items = array_merge($items, $data['name']);
                }
            }

            usort($items, function ($a, $b) {
                if (in_array($b['provider_Name'], self::$highlightedProviders)) return 1;
                return -1;
            });

            Cache::put('Nuxgame:providersList', $items, Carbon::now()->addDays(1));

            return $items;
        } catch (\Exception $e) {
            Log::error($e);
            return [];
        }
    }

    public function getGameList(): array
{
    $url = '/gameList'; // Endpoint for fetching the game list
    $response = self::request('get', $url); // Adjusted to use the request method correctly

    if ($response['status'] !== 200) {
        // Handle error, maybe log it
        return [];
    }

    $data = $response['data'];

    if (!isset($data['data'])) {
        // Handle missing data
        return [];
    }

    return $data['data']; // Return the game list
}

}
