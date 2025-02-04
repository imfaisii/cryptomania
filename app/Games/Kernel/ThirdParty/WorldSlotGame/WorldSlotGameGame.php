<?php

namespace App\Games\Kernel\ThirdParty\WorldSlotGame;

use App\Events\BalanceModification;
use App\Games\Kernel\Data;
use App\Games\Kernel\GameCategory;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\ThirdParty\ThirdPartyGame;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WorldSlotGameGame extends ThirdPartyGame
{
  public function __construct($data = null)
  {
    parent::__construct($data);
  }

  public function provider(): string
  {
    return $this->metadata()->provider()['name'];
  }

  function metadata(): ?Metadata
  {
    if (!$this->data) return null;

    return new class($this->data) extends Metadata
    {
      private ?array $data;

      public function __construct(?array $data)
      {
        $this->data = $data;
      }

      function id(): string
      {
        return 'external:wsg:' . $this->data['id'];
      }

      function name(): string
      {
        return $this->data['name'];
      }

      function icon(): string
      {
        return "slots";
      }

      public function image(): string
      {
        return $this->data['img_vertical'];
      }

      public function demoable(): string
      {
        return $this->data['demo'];
      }

      public function category(): array
      {
        $categories = [];
        
        // Log::info('games-for-category----------'); Log::info($this->data);

        if (isset($this->data['typeId'])) {
          if ($this->data['typeId'] === 1) $categories[] = GameCategory::$live;
          if ($this->data['typeId'] === 2) $categories[] = GameCategory::$slots;
        }

        // if (isset($this->data['provider'])) {
        //   if ($this->data['provider']['category'] === 2) $categories[] = GameCategory::$live;
        //   if ($this->data['provider']['category'] === 1) $categories[] = GameCategory::$slots;
        // }

        return $categories;
      }

      public function round()
      {
        return $this->data['round_id'];
      }

      public function provider(): array
      {
        return $this->data['provider'];
      }

      public function transaction(): array
      {
        return $this->data['transaction'];
      }
    };
  }

  public function processCallback(\App\Currency\Currency $currency): array
  {
    
    try {
      // $lock = Cache::lock($this->metadata()->id() . 'nuxgame', 5);
      // if($lock->get()) {
        $metadata = $this->metadata();
        $transaction = $metadata->transaction();
        $user = \App\Models\User::where('_id', $transaction['userId'])->first();
        $transactionID = $transaction['transactionId'];
        $transactionType = $transaction['direction'];
        $betMoney = $currency->convertEURToToken(floatval($transaction['bet']));
        $winMoney = $currency->convertEURToToken(floatval($transaction['win']));
        $userBalance = $user->balance($currency);
        $balance = $userBalance->get();
        $balanceEUR = $currency->fiatNumberFormat($currency->convertTokenToEUR($balance));
        // Log::info('balance---'.$balance.'----------'.$balanceEUR);
        // Log::info('bet-fiat---'.$transaction['bet'].'////bet-crypto---'.$betMoney);
        // Log::info('win-fiat---'.$transaction['win'].'////win-crypto---'.$winMoney);
        // Log::info($transaction);

        if (($transaction['direction'] == 'debit' && $transaction['eventType'] == 'Win') || ($transaction['direction'] == 'credit' && $transaction['eventType'] == 'BetPlacing')) return [
          'status' => false,
          'errors' => [
            'code' => 400,
            'error' => 'WRONG EVENT TYPE'
          ]
        ];

        if ($betMoney > $balance) return [
          'status' => false,
          'errors' => [
            'code' => 402,
            'error' => 'Insufficient funds to place current wager'
          ],
          'balance' => $balanceEUR
        ];

        $game = \App\Models\Game::where('ss_gameId', $metadata->round())->first(); // ->where('user', $user->_id)

        // Log::info('game-------');
        // Log::info($game);
        // Log::info('game-round-id---'.$metadata->round().'///'.(!$game?'null':$game->ss_gameId));
        
        if (\App\Models\Transaction::where('service_id', $transactionID)->exists()) {      
          $old_trans = \App\Models\Transaction::where('service_id', $transactionID)->first();
          Log::info('old_trans-------'.$transactionID);
          Log::info($old_trans);
          if (($transactionType == 'credit' && $game != null && ($old_trans['service_type'] === 'win')) || $transactionType == 'debit')  
          return [
            'status' => false,
            'errors' => [
              'code' => 409,
              'error' => 'DUPLICATED REQUEST'
            ],
            'balance' => $balanceEUR
          ];
        }

        if ($transaction['eventType'] == 'BetPlacingAbort' && $game != null && $game->status == 'win') return [
          'status' => false,
          'errors' => [
            'code' => 406,
            'error' => 'WRONG ROLLBACK'
          ],
          'balance' => $balanceEUR
        ];

        if ($transaction['eventType'] == 'BetPlacingAbort' && $game == null) return [
          'status' => false,
          'errors' => [
            'code' => 409,
            'error' => 'DUPLICATED ROLLBACK'
          ],
          'balance' => $balanceEUR
        ];

        // if ($transactionType == 'debit' && $game != null) {
        //   return [
        //     'status' => false,
        //     'errors' => [
        //       'code' => 406,
        //       'error' => 'WRONG EVENT ID'
        //     ],
        //     'balance' => $balanceEUR
        //   ];
        // }

        if ($transactionType == 'credit') {
          if ($game == null) {
            return [
              'status' => false,
              'errors' => [
                'code' => 406,
                'error' => 'WRONG EVENT ID'
              ],
              'balance' => $balanceEUR
            ];
          } else {    
            if ($transaction['userId'] !== $game['user'])  {       
              $user_old = \App\Models\User::where('_id', $game['user'])->first();    
              $userBalance_old = $user_old->balance($currency);
              $balance_old = $userBalance_old->get();
              $balanceEUR_old = $currency->fiatNumberFormat($currency->convertTokenToEUR($balance_old));

              return [
                'status' => false,
                'errors' => [
                  'code' => 406,
                  'error' => 'WRONG EVENT ID'
                ],
                'balance' => $balanceEUR_old
              ];
            }
          }
        }

        if ($game == null) {
          $game = \App\Models\Game::create([
            'id' => DB::table('games')->count() + 1,
            'user' => $user->_id,
            'game' => $metadata->id(),
            'multiplier' => 0,
            'status' => 'in-progress',
            'profit' => 0,
            'server_seed' => $this->server_seed(),
            'client_seed' => $this->client_seed(),
            'data' => [],
            'type' => 'third-party',
            'demo' => false,
            'wager' => $betMoney,
            'currency' => $currency->id(),
            'ss_gameId' => $metadata->round(),
            'bet_usd_converted' => $betMoney
          ]);
        }

        $game->update(['status' => 'in-progress']);

        $profit = $game->profit;

        if ($betMoney > 0) {
          
          $game->update([
            'status' => 'lose',
          ]);

          $userBalance->subtract(
            $betMoney,
            Transaction::builder()->game($metadata->name())->provider($metadata->provider()['name'])->message("Status: {$transaction['eventType']} / Transaction ID: {$game->ss_gameId}")->wager($transaction['bet'])->profit(0)->get(),
            $transactionID,
            'bet'
          );

        }

        if ($winMoney > 0) {

          $profit += $winMoney;
          $payout = $profit > 0 && $game->wager > 0 ? $profit / $game->wager : 0;

          $game->update([
            'multiplier' => $payout,
            'status' => $payout > 0 ? 'win' : 'lose',
            'profit' => $profit
          ]);

          $userBalance->add(
            $winMoney,
            Transaction::builder()->game($metadata->name())->provider($metadata->provider()['name'])->message("Status: {$transaction['eventType']} / Transaction ID: {$game->ss_gameId}")->wager($transaction['win'])->profit($profit)->get(),
            // Transaction::builder()->game($metadata->name())->message("Ação: vitória / ID transação: {$game->ss_gameId}")->get(),
            0,
            $transactionID,
            'win'
          );

          try {
            self::analytics($game, 'Providers');
            event(new \App\Events\LiveFeedGame($game, 0));
          } catch (\Exception $ignored) {
            //
          }
        }

        if (($transactionType === 'credit') && $winMoney == 0) {
          try {
            self::analytics($game, 'Providers');
            event(new \App\Events\LiveFeedGame($game, 0));
          } catch (\Exception $ignored) {
            //
          }
        }

        $payout = $profit > 0 && $game->wager > 0 ? $profit / $game->wager : 0;

        $game->update([
          'multiplier' => $payout,
          'status' => $payout > 0 ? 'win' : 'lose',
          'profit' => $profit
        ]);
        
        if ($transaction['eventType'] == 'BetPlacingAbort') {
          $game->delete();
        }

        // Log::info('game-update-return-balance--------------::: ' . $transactionID . '//' . $userBalance->get() . '////' . $currency->fiatNumberFormat($currency->convertTokenToEUR($userBalance->get())));
        // $lock->release();
        return [
          'status' => true,
          'balance' => $currency->fiatNumberFormat($currency->convertTokenToEUR($userBalance->get()))
        ];
      // } else {
      //   return [
      //     'status' => false,
      //     'errors' => [
      //       'code' => 400,
      //       'error' => 'LOCK_REQUEST_HANDLING'
      //     ]
      //   ];
      // }
    } catch (\Exception $exception) {
      Log::error('process-callback-error: '. $exception->getMessage());
      return [
        'status' => false,
        'errors' => [
          'code' => 400,
          'error' => 'INTERNAL ERROR'
        ]
      ];
    }
    
  }

  function process(Data $data): array
  {
    $metadata = $this->metadata();
    //$currencySetting = \App\Models\Settings::get('currencies', 'local_suitpay');
    // $currencyData = json_decode($currencySetting, true)[0];
    $currency = \App\Currency\Currency::find($data->currency());

    // Log::info('process-data----- '.$data->demo());
    // Log::info(json_encode($data));

    if ($data->demo()) {

      $demoable = $metadata->demoable();

      if (!$demoable) {
        return ['error' => ['message' => `This game doesn't provide demo play.`]];
      }

      $link = WorldSlotGame::keys()['apiUrl'].'/start?demo=true&gameId='.str_replace("external:wsg:", "", $metadata->id());

      return [
        'response' => [
          'id' => '-1',
          'wager' => $data->bet(),
          'type' => 'third-party',
          'link' => $link
        ]
      ];
    }

    $hashAuthorizationKey = WorldSlotGame::keys()['agentToken']; // '424c65e51942160021fefe9d6d603492'; // Key from back office

    $paybody = [
      'demo' => false,
      'userId' => $data->user()->_id,
      'gameId' => str_replace("external:wsg:", "", $metadata->id())
    ];

    ksort($paybody);
    $paybody = array_map('strval', $paybody);
    $paybody = json_encode($paybody);

    $token = hash('sha256', $paybody . $hashAuthorizationKey);
    $data->user()->setGameToken($token);
    $data->user()->save();

    // Log::info('process-token----- '.$data->user()->_id.'///'.str_replace("external:wsg:", "", $metadata->id()).'///'.$token);

    $link = WorldSlotGame::keys()['apiUrl'].'/start?demo=false&token='.$token.'&userId='.$data->user()->_id.'&gameId='.str_replace("external:wsg:", "", $metadata->id());

    return [
      'response' => [
        'id' => '-1',
        'wager' => $data->bet(),
        'type' => 'third-party',
        'link' => $link
      ]
    ];
  }

  public function createInstances(): array
  {
    $games = [];

    $blacklist = [
      '' // id game
    ];

    $gameNames = [];

    foreach ($this->data as $game) {
      $name = $game['name'];

      if (!in_array($name, $gameNames) && !in_array($game['id'], $blacklist)) {
        $games[] = new WorldSlotGameGame($game);
        $gameNames[] = $name;
      }
    }

    return $games;
  }
}
