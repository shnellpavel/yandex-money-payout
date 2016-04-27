<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:35
 */

namespace YandexMoney\interfaces;


use YandexMoney\BalanceRequestParams;
use YandexMoney\DepositionRequestParams;
use YandexMoney\ErrorDepositionParams;

interface IPayoutAPI
{
    public function makeDeposition( DepositionRequestParams $params );

    public function testDeposition( DepositionRequestParams $params );

    public function getBalance( BalanceRequestParams $params );

    public function errorDepositionNotification( ErrorDepositionParams $params );
} 