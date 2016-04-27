<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:51
 */

namespace YandexMoney;


use YandexMoney\interfaces\IXMLTransformable;

class DepositionRequestParams implements IXMLTransformable
{
    private $agentId;
    private $clientOrderId;
    private $reqType;
    private $paymentParams;

    public $amount;
    public $currency = '10643'; //643
    public $contract = '';
    public $dstAccount;

    public function __construct( $agentId, $clientOrderId, $reqType )
    {
        $this->agentId       = $agentId;
        $this->clientOrderId = $clientOrderId;
        $this->reqType       = $reqType;
    }

    public function setPaymentParams(PaymentParams $params)
    {
        $this->paymentParams = $params;
    }

    public function toXml()
    {
        $result = new \SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$this->reqType}Request/>" );

        $result->addAttribute( 'agentId', $this->agentId );
        $result->addAttribute( 'clientOrderId', $this->clientOrderId );
        $result->addAttribute( 'requestDT', date( 'Y-m-d\TH:i:s.000\Z' ) );
        $result->addAttribute( 'dstAccount', $this->dstAccount );
        $result->addAttribute( 'amount', sprintf("%.2f", $this->amount) );
        $result->addAttribute( 'currency', $this->currency );
        $result->addAttribute( 'contract', $this->contract );

        return $result->asXML();
    }

}