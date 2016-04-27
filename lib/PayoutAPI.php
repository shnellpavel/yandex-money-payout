<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:35
 */

namespace YandexMoney;


use YandexMoney\interfaces\IDispositionRequestProvider;
use YandexMoney\interfaces\IPayoutAPI;

class PayoutAPI implements IPayoutAPI
{
    private $requestProvider;

    public function __construct( IDispositionRequestProvider $requestProvider )
    {
        $this->requestProvider = $requestProvider;
    }

    public function makeDeposition( DepositionRequestParams $params )
    {
        return $this->requestProvider->sendRequest( 'makeDeposition', $params );
    }

    public function testDeposition( DepositionRequestParams $params )
    {
        return $this->requestProvider->sendRequest( 'testDeposition', $params );
    }

    public function getBalance( BalanceRequestParams $params )
    {
        return $this->requestProvider->sendRequest( 'balance', $params );
    }

    public function errorDepositionNotification( ErrorDepositionParams $params )
    {
        return $this->requestProvider->processRequest($params); // ??
    }
}