<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;

class Transaction extends Model {

    protected $collection = 'transactions';
    protected $connection = 'mongodb';

    protected $fillable = [
        'user', 'demo', 'currency', 'data', 'new', 'old', 'amount', 'quiet', 'service_id', 'service_type', 'refund_processed'
    ];

    protected $casts = [
        'data' => 'json'
    ];

    public static function isProcessed(string $serviceId): bool {
        return Transaction::where('service_id', $serviceId)->first() != null;
    }

    // public static function builder() {
    //     return new class {

    //         private array $result = [];

    //         public function game(string $game_id) {
    //             $this->result['game'] = $game_id;
    //             return $this;
    //         }

    //         public function message(string $message) {
    //             $this->result['message'] = $message;
    //             return $this;
    //         }

    //         public function get(): array {
    //             return $this->result;
    //         }

    //     };
    // }
    public static function builder()
    {
        return new class
        {
            private array $result = [];

            public function game(string $gameID)
            {
                $this->result['game'] = $gameID;
                return $this;
            }

            public function provider(string $provider)
            {
                $this->result['provider'] = $provider;
                return $this;
            }

            public function message(string $message)
            {
                $this->result['message'] = $message;
                return $this;
            }

            public function wager($wager)
            {
                $this->result['wager'] = $wager;
                return $this;
            }

            public function profit($profit)
            {
                $this->result['profit'] = $profit;
                return $this;
            }

            public function get(): array
            {
                return $this->result;
            }
        };
    }

}
