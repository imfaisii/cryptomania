<?php namespace App\Currency\Commerce;

class CommerceBNB extends CommerceCurrency {

  function id(): string {
    return "commerce_bnb";
  }

  public function walletId(): string {
    return "bnb";
  }

  function name(): string {
    return "BNB";
  }

  public function alias(): string {
    return "binancecoin";
  }

  public function displayName(): string {
    return "BNB";
  }

  function icon(): string {
    return "Image/img/BNB.png"; // bnb.svg
  }

  function style(): string {
    return "#F0B90B";
  }

  public function coinbaseId(): string {
    return 'BNB';
  }

  public function coinbaseName(): string {
    return 'BNB';
  }

  public function coinpaymentsId(): string {
    return 'BNB';
  }

}
