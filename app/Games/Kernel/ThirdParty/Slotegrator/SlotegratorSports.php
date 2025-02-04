<?php namespace App\Games\Kernel\ThirdParty\Slotegrator;

use App\Models\Settings;
use App\Currency\Currency;
use App\Games\Kernel\Data;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\ProvablyFair;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SlotegratorSports {

  public static function keys(): array {
    return [
      'merchantId' => 'c6f209a4e540a228c8568d4c382e46d0',
      'merchantKey' => 'daa7f6e0b5ac7c9833be88b352d95d6957add064',
      'apiUrl' =>'https://bet.ga-stage.work/api/v1'
    ];
  }

  public static function debug(): bool {
    return 'false';
  }

  public static function type(): string {
    return 'staging';
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
      'Content-type' => 'application/json',
      // 'Enctype' => 'application/x-www-form-urlencoded',
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
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
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

  public static function getSportsURL(array $data) {
    Log::info('slotegrator-sportsbooks-data---', $data);
    $response = self::request('/sportsbooks', [], 'get')['data'];
    Log::info('slotegrator-sportsbooks-response---', $response[0]);

    $response1 = self::request('/sportsbooks/init', [
      'sportsbook_uuid' => $response[0]['uuid'],
      'player_id' => $data['_id'],
      'player_name' => $data['name'],
      'currency' => 'EUR',
      'session_id' => ProvablyFair::generateServerSeed()
    ]);
    Log::info('slotegrator-sportsbooks-link---', $response1['data']);

      // if (!isset($response['data']['url'])) {
      //   switch ($response['status']) {
      //     case 403:
      //       $message = 'The authenticated user is not allowed to access the specified API endpoint.';
      //       break;
      //     case 401:
      //       $message = 'Authentication failed.';
      //       break;
      //     default:
      //       Log::error('Slotegrator error');
      //       Log::error($response);
      //       $message = 'Error during game initialization';
      //       break;
      //   }
      //   return ['error' => ['message' => $message]];
      // }

      $curl = curl_init($response1['data']['url']);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      
      // Step 3: Execute cURL request to get HTML content
      $htmlContent = curl_exec($curl);
      Log::info('Curl-htmlContent-------------: ' . $htmlContent);

      // Check for cURL errors
      if(curl_errno($curl)) {
          Log::info('Curl error: ' . curl_error($curl));
          curl_close($curl);
          exit();
      }
      
      // Close cURL connection
      curl_close($curl);

      return [
        'token' => $response1['data']['token'],
        'htmlContent' => $htmlContent,
        'link' => $response1['data']['url']
      ];

    // } else {
    //   // throw new UnsupportedOperationException();
    //   return ['error' => ['message' => 'Unsupported Operation']];
    // }
  }

  // public function sefValidate(): void {
  //   $data = self::request('/self-validate', [], 'post');
  //   Log::info('slotegrator-self-validate-result', $data);
  // }
}
