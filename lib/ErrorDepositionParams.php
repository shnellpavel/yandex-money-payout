<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:57
 */

namespace YandexMoney;


use YandexMoney\interfaces\IXMLTransformable;

class ErrorDepositionParams implements IXMLTransformable
{
    private $clientOrderId;
    public $status = 0;

    public function __construct( $clientOrderId )
    {
        $this->clientOrderId = $clientOrderId;
    }

    public function asXml()
    {
        $result = new \SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?><errorDepositionNotificationResponse/>" );

        $result->addAttribute( 'status', $this->status );
        $result->addAttribute( 'clientOrderId', $this->clientOrderId );
        $result->addAttribute( 'requestDT', date( 'Y-m-d\TH:i:s.000\Z' ) );

        return $result;
    }

    public function toXml()
    {
        $this->asXml()->asXML();
    }
}