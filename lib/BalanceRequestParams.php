<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:50
 */

namespace YandexMoney;


use YandexMoney\interfaces\IXMLTransformable;

class BalanceRequestParams implements IXMLTransformable
{
    private $agentId;
    private $clientOrderId;

    public function __construct( $agentId, $clientOrderId )
    {
        $this->agentId       = $agentId;
        $this->clientOrderId = $clientOrderId;
    }

    public function toXml()
    {
        $result = new \SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><balanceRequest/>' );

        $result->addAttribute( 'agentId', $this->agentId );
        $result->addAttribute( 'clientOrderId', $this->clientOrderId );
        $result->addAttribute( 'requestDT', date( 'Y-m-d\TH:i:s.000\Z' ) );

        return $result->asXML();
    }
}