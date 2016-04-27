<?php
/**
 * User: shnell
 * Date: 28.04.16
 * Time: 0:22
 */

namespace YandexMoney;


class MobilePaymentParams extends PaymentParams
{
    public $operatorCode;
    public $phoneNumber;


    public function asXml()
    {
        $result = parent::asXml();

        $result->addChild( 'PROPERTY1', $this->operatorCode );
        $result->addChild( 'PROPERTY2', $this->phoneNumber );

        return $result;
    }
}