<?php namespace App\Currency\Commerce;

class CommerceXRP extends CommerceCurrency {

  function id(): string {
    return "commerce_xrp";
  }

  public function walletId(): string {
    return "xrp";
  }

  function name(): string {
    return "XRP";
  }

  public function alias(): string {
    return "binance-peg-xrp";
  }

  public function displayName(): string {
    return "XRP";
  }

  function icon(): string {
    return "xrp";
  }

  function style(): string {
    return "#397fb0";
  }

  public function coinbaseId(): string {
    return 'XRP';
  }

  public function coinbaseName(): string {
    return 'XRP';
  }

  public function coinpaymentsId(): string {
    return 'XRP';
  }

}
