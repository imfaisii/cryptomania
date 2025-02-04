<?php namespace App\Games\Kernel\ThirdParty\Nuxgame;

use App\Currency\Currency;
use App\Games\Kernel\Data;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\ProvablyFair;
use App\Games\Kernel\ThirdParty\ThirdPartyGame;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBalance;
use App\Utils\Exception\UnsupportedOperationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NuxgameGame extends ThirdPartyGame {

  private string $providerId;

  public function __construct(string $providerId, ?array $data = null) {
    parent::__construct($data);

    $this->providerId = $providerId;
  }

  public function provider(): string {
    return $this->providerId;
  }

  function metadata(): ?Metadata {
    if (!$this->data) return null;

    return new class($this->data) extends Metadata {
      private ?array $data;

      public function __construct(?array $data) {
        $this->data = $data;
      }

      function id(): string {
        return 'external:ng:' . $this->data['id'];
      }

      function name(): string {
        return str_replace(" Mobile", "", $this->data['name']);
      }

      function icon(): string {
        return "slots";
      }

      public function image(): string {
        return $this->data['img'];
      }

      public function category(): array {
    $categories = [$this->data['provider']];

    // Determine the category based on game type
    switch ($this->data['type']) {
        case 'Live':
            $categories[] = 'live';
            break;
        case 'Slots':
            $categories[] = 'slots';
            break;
        case 'Virtuals':
            $categories[] = 'virtuals';
            break;
        case 'TvGames':
            $categories[] = 'tv_games';
            break;
        case 'Poker':
            $categories[] = 'poker';
            break;
        case 'SportBook':
            $categories[] = 'sportsbook';
            break;
        default:
            $categories[] = 'other';
            break;
    }

    return $categories;
}


      public function isMobile(): ?bool {
        return $this->data['device'] === 1 || str_contains(strtolower($this->name()), "mobile");
      }
    };
  }

  function process(Data $data) {
    $metadata = $this->metadata();

    if (!in_array('live', $metadata->category())) {
      if ($data->demo() || $data->user() == null) {
        $response = Nuxgame::request('/gameList', [
          'id' => str_replace("external:ng:", "", $metadata->id())
        ]);
      } else {
        $response = Nuxgame::request('/gameList', [
          'id' => str_replace("external:ng:", "", $metadata->id()),
          'userId' => $data->user()->_id . '-' . $data->currency(),
          'player_name' => $data->user()->name,
          'demo' => 'true',
          'token' => ProvablyFair::generateServerSeed()
        ]);
      }

      if (!isset($response['data']['url'])) {
        switch ($response['status']) {
          case 403:
            $message = 'The authenticated user is not allowed to access the specified API endpoint.';
            break;
          case 401:
            $message = 'Authentication failed.';
            break;
          default:
            Log::error('Nuxgame error');
            Log::error($response);
            $message = 'Error during game initialization';
            break;
        }
        return ['error' => ['message' => $message]];
      }

      return [
        'response' => [
          'id' => '-1',
          'wager' => $data->bet(),
          'type' => 'third-party',
          'link' => $response['data']['url']
        ]
      ];
    } else {
      throw new UnsupportedOperationException();
    }
  }

  public function createInstances(): array {
    $games = [];

    $blacklist = [
      '3a70598171a689fe57eb4ccab7333bacac26c1b9' // Quickspin "test"
    ];

    $gameNames = [];

    foreach ($this->data as $game) {
      $name = str_replace(" Mobile", "", $game['name']);

      if (!in_array($name, $gameNames) && !in_array($game['id'], $blacklist) && $game['name'] === 'HTML5') {
        $games[] = new NuxgameGame($this->providerId, $game);
        $gameNames[] = $name;
      }
    }

    return $games;
  }

}
