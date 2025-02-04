<?php namespace App\Events;

use App\Models\Game;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveFeedGame implements ShouldBroadcastNow {

  use Dispatchable, InteractsWithSockets, SerializesModels;

  private $game;

  public function __construct(Game $game) {
    $this->game = $game;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel
   */
  public function broadcastOn() {
    return new Channel('Everyone');
  }

  public function broadcastWith() {
    return [
      'game' => $this->game->toArray(),
      'user' => User::where('_id', $this->game->user)->first()->toArray(),
      // 'user' => (isset(User::where('_id', $this->game->user)->first()->private_bets) && User::where('_id', $this->game->user)->first()->private_bets) ? 
      //     array_replace(User::where('_id', $this->game->user)->first()->toArray(), ['name' => '???']) : User::where('_id', $this->game->user)->first()->toArray(),
      'metadata' => \App\Games\Kernel\Game::find($this->game->game) ? \App\Games\Kernel\Game::find($this->game->game)->metadata()->toArray() : [],
    ];
  }

}
