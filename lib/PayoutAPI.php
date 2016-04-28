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
    const REQ_STATUS_SUCCESS     = 0;
    const REQ_STATUS_IN_PROGRESS = 1;
    const REQ_STATUS_REJECTED    = 3;

    private $requestProvider;
    private $synonimUrl;

    public function __construct( IDispositionRequestProvider $requestProvider, $synonimUrl )
    {
        $this->requestProvider = $requestProvider;
    }

    public function getCardSynonim( $cardNum )
    {
        $curl   = curl_init();
        $params = array(
            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_HTTPHEADER     => array( 'Content-type: application/pkcs7-mime' ),
            CURLOPT_URL            => $this->synonimUrl,
            CURLOPT_POST           => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE        => 0,
            CURLOPT_POSTFIELDS     => array(
                'skr_destinationCardNumber' => $cardNum,
                'skr_responseFormat'        => 'json',
            )
        );
        curl_setopt_array( $curl, $params );

        $result = null;
        try
        {
            $result = curl_exec( $curl );
            if ( !$result )
            {
                trigger_error( curl_error( $curl ) );
            }
            curl_close( $curl );

            $result = json_decode( $result );
            $result = $result[ 'storeCard' ];
        } catch ( \HttpException $ex )
        {
            echo $ex;
        }

        return $result;
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

    public function errorDepositionNotification( $handler )
    {
        return $this->requestProvider->processRequest( $handler );
    }
}