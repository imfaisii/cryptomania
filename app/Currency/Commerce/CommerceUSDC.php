<?php namespace App\Currency\Commerce;

class CommerceUSDC extends CommerceCurrency {

  function id(): string {
    return "commerce_usdc";
  }

  public function walletId(): string {
    return "usdc";
  }

  function name(): string {
    return "USDC.TRC20";
  }

  public function alias(): string {
    return "usd-coin";
  }

  public function displayName(): string {
    return "USD Coin (USDC)";
  }

  function icon(): string {
    return "usdc";
  }

  function style(): string {
    return "#2775ca";
  }

  public function coinbaseId(): string {
    return 'USDC';
  }

  public function coinbaseName(): string {
    return 'USDC';
  }

  public function coinpaymentsId(): string {
    return 'USDC.TRC20';
  }

}
