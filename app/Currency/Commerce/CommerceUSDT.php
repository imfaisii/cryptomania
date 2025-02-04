<?php namespace App\Currency\Commerce;

class CommerceUSDT extends CommerceCurrency {

  function id(): string {
    return "commerce_usdt";
  }

  public function walletId(): string {
    return "usdt";
  }

  function name(): string {
    return "USDT.TRC20";
  }

  public function alias(): string {
    return "tether";
  }

  public function displayName(): string {
    return "Tether (USDT)";
  }

  function icon(): string {
    return "tether";
  }

  function style(): string {
    return "lightgreen";
  }

  public function coinbaseId(): string {
    return 'USDT';
  }

  public function coinbaseName(): string {
    return 'USDT';
  }

  public function coinpaymentsId(): string {
    return 'USDT.TRC20';
  }

}
