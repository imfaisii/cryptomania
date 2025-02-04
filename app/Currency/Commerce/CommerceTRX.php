<?php namespace App\Currency\Commerce;

class CommerceTRX extends CommerceCurrency {

  function id(): string {
    return "commerce_trx";
  }

  public function walletId(): string {
    return "trx";
  }

  function name(): string {
    return "TRX";
  }

  public function alias(): string {
    return "tron";
  }

  public function displayName(): string {
    return "TRX";
  }

  function icon(): string {
    return "trx";
  }

  function style(): string {
    return "#c80909";
  }

  public function coinbaseId(): string {
    return 'TRX';
  }

  public function coinbaseName(): string {
    return 'TRON';
  }

  public function coinpaymentsId(): string {
    return 'TRX';
  }

}
