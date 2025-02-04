<?php namespace App\Currency\Commerce;

class CommerceRVN extends CommerceCurrency {

    function id(): string {
        return "commerce_rvn";
    }

    public function walletId(): string {
        return "rvn";
    }

    function name(): string {
        return "RVN";
    }

    public function alias(): string {
        return "ravencoin";
    }

    public function displayName(): string {
        return "Ravencoin";
    }

    function icon(): string {
    return "Image/img/RVN.png"; // bnb.svg
    }

    function style(): string {
    return "#384182"; // Ravencoin color
}


    public function coinbaseId(): string {
        return 'RVN';
    }

    public function coinbaseName(): string {
        return 'Ravencoin';
    }

    public function coinpaymentsId(): string {
        return 'RVN';
    }

}
