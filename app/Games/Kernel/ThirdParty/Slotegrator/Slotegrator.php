<?php namespace App\Games\Kernel\ThirdParty\Slotegrator;

use App\Games\Kernel\ProvablyFair;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Slotegrator {

  private static array $highlightedProviders = [
    '3Oaks',
    'Amatic',
    'AmigoGaming',
    'BelatraGames',
    'BetGames',
    'Betsoft',
    'Betsolutions',
    'BFGames',
    'BGaming',
    'BigTimeGaming',
    'Blueprint',
    'CaletaGaming',
    'ConceptGaming',
    'CTInteractive',
    'DLV',
    'Elbet',
    'Endorphina',
    'EspressoGames',
    'EURASIANGaming',
    'Evolution',
    'Ezugi',
    'G.Games',
    'GameBeat',
    'Gamzix',
    'HacksawGaming',
    'Igrosoft',
    'Justplay',
    'KAGaming',
    'KalambaGames',
    'LottoInstantWin',
    'MacawGaming',
    'MascotGaming',
    'NetEnt',
    'NetGameEntertainment',
    'NolimitCity',
    'OneTouch',
    'PGSOFT',
    'PlatipusGaming',
    'Playson',
    'PragmaticPlay',
    'PushGaming',
    'Quickspin',
    'RedTigerGaming',
    'REEVO',
    'RTGSlots',
    'SmartsoftGaming',
    'Spadegaming',
    'Spinmatic',
    'SuperSpadeGames',
    'Thunderkick',
    'TomHornGaming',
    'TPG',
    'TripleCherry',
    'TurboGames',
    'TurboSportsbook',
    'VibraGaming',
    'VivoGaming',
    'Wazdan',
    'XPG',
    'Yggdrasil'
];


  public static function keys(): array {
    return [
      'merchantId' => Settings::get('[Slotegrator] Merchant ID', 'f479788422ddfb32785175af18ed06a6'),
      'merchantKey' => Settings::get('[Slotegrator] Merchant Key', 'f0778e828ed260faf8a9f6c40752ff36f159da3c'),
      'apiUrl' => Settings::get('[Slotegrator] API URL', 'https://gis.slotegrator.com/api/index.php/v1')
    ];
  }

  public static function debug(): bool {
    return Settings::get('[Slotegrator] Debug', 'false') === 'true';
  }

  public static function type(): string {
    return Settings::get('[Slotegrator] Key Type', 'staging');
  }

  public static function request(string $url, array $body = [], string $method = 'post'): array {
    $session = ProvablyFair::generateServerSeed();

    $headers = [
      "X-Merchant-Id" => self::keys()['merchantId'],
      "X-Timestamp" => time(),
      "X-Nonce" => $session
    ];

    if(self::debug()) {
      Log::info(self::keys());
    }

    $mergedParams = array_merge($body, $headers);
    ksort($mergedParams);
    $hashString = http_build_query($mergedParams);
    $XSign = hash_hmac('sha1', $hashString, self::keys()['merchantKey']);

    $headers = array_merge($headers, [
      "X-Sign" => $XSign,
      'Accept' => 'application/json',
      'Enctype' => 'application/x-www-form-urlencoded',
    ]);

    // Format key => value headers to CURLOPT_HTTPHEADER format
    $curlFormattedHeaders = [];
    foreach ($headers as $key => $value)
      $curlFormattedHeaders = array_merge($curlFormattedHeaders, [$key . ': ' . $value]);

    if(self::debug()) Log::info("Request: " . $url . " " . json_encode($body) . " " . json_encode($curlFormattedHeaders));

    $curl = curl_init(self::keys()['apiUrl'] . $url . (count($body) > 0 && $method == 'get' ? '?' . http_build_query($body) : ''));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $curlFormattedHeaders);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

    if ($method === 'post') {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($body));
    }

    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if(self::debug()) Log::info('Slotegrator response: ' . $status . " " . $json_response);

    curl_close($curl);

    return [
      'data' => json_decode($json_response, true),
      'status' => $status,
      'nonce' => $session
    ];
  }

  public function getProviders(): array {
      Cache::forget('slotegrator:loadingGameList');
    if(Cache::has('slotegrator:loadingGameList')) {
      // Log::info('slotegrator-loadingGameList');
      return [];
    }

    if (Cache::has('slotegrator:providerGameList')) {
     // Log::info('slotegrator-providerGameList');
      $providers = [];
      $items = Cache::get('slotegrator:providerGameList');

      $providerIds = [];
      foreach ($items as $item)
        if (!in_array($item['provider'], $providerIds)) $providerIds[] = $item['provider'];

      foreach ($providerIds as $providerId) {
        $games = array_filter($items, function ($e) use ($providerId) {
          return $e['provider'] == $providerId;
        });

        $provider = new SlotegratorGame($providerId, $games);
        if (count($provider->createInstances()) > 0) $providers[] = $provider;
      }

      return $providers;
    }

    try {
      Cache::put('slotegrator:loadingGameList', 'true');
      // Log::info('slotegrator-try-');
      $data = self::request('/games', [], 'get')['data'];
      Log::info('slotegrator-', $data['items']);
      $maxPage = $data['_meta']['pageCount'];
      $items = $data['items'];
      for ($i = 2; $i <= $maxPage; $i++) {
        $data = self::request('/games/index', ['page' => $i], 'get')['data'];
        if(isset($data['items'])) $items = array_merge($items, $data['items']);

        // Log::info('slotegrator-for-', [$i, $data['items']['type']]);

        sleep(1); // anti rate-limit
      }

      // Log::info('slotegrator-final-', $items);

      usort($items, function ($a, $b) {
        if (in_array($b['provider'], self::$highlightedProviders)) return 1;
        return -1;
      });

      Cache::put('slotegrator:providerGameList', $items, Carbon::now()->addDays(1));
      Cache::forget('slotegrator:loadingGameList');
      Cache::forget('game:list');
      return $this->getProviders();
    } catch (\Exception $e) {
      Log::error($e);
      Cache::forget('slotegrator:loadingGameList');
      return [];
    }
  }

  public function sefValidate(): void {
    $data = self::request('/self-validate', [], 'post');
    Log::info('slotegrator-self-validate-result', $data);
  }
}
