<?php namespace App\Currency\Commerce;

class CommerceSOL extends CommerceCurrency {

  function id(): string {
    return "commerce_sol";
  }

  public function walletId(): string {
    return "sol";
  }

  function name(): string {
    return "SOL";
  }

  public function alias(): string {
    return "solana";
  }

  public function displayName(): string {
    return "Solana";
  }

  function icon(): string {
    return "sol";
  }

  function style(): string {
    return "#627eea";
  }

  public function coinbaseId(): string {
    return 'SOL';
  }

  public function coinbaseName(): string {
    return 'SOL';
  }

  public function coinpaymentsId(): string {
    return 'SOL';
  }

}
