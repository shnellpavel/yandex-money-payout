<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:51
 */

namespace YandexMoney;


use YandexMoney\interfaces\IXMLTransformable;

abstract class PaymentParams implements IXMLTransformable
{
    public function toXml()
    {
        return $this->asXml()->asXML();
    }

    public function asXml()
    {
        $result = new \SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?><paymentParams/>" );

        $result->addChild( 'pof_offerAccepted', 1 );

        return $result;
    }
} 