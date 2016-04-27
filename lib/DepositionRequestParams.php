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
    const ACCOUNT_MTS     = 2570066959750;
    const ACCOUNT_MEGAFON = 2570066959438;
    const ACCOUNT_TELE2   = 25700583516540;
    const ACCOUNT_BILINE  = 2570066957329;

    const MOBILE_OPERATOR_MTS     = 'mts';
    const MOBILE_OPERATOR_MEGAFON = 'megafon';
    const MOBILE_OPERATOR_TELE2   = 'tele2';
    const MOBILE_OPERATOR_BILINE  = 'biline';

    private $agentId;
    private $clientOrderId;
    private $reqType;
    /**
     * @var PaymentParams
     */
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

    public function setPaymentParams( PaymentParams $params )
    {
        $this->paymentParams = $params;
    }

    public function asXml()
    {
        $result = new \SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$this->reqType}Request/>" );

        $result->addAttribute( 'agentId', $this->agentId );
        $result->addAttribute( 'clientOrderId', $this->clientOrderId );
        $result->addAttribute( 'requestDT', date( 'Y-m-d\TH:i:s.000\Z' ) );
        $result->addAttribute( 'dstAccount', $this->dstAccount );
        $result->addAttribute( 'amount', sprintf( "%.2f", $this->amount ) );
        $result->addAttribute( 'currency', $this->currency );
        $result->addAttribute( 'contract', $this->contract );

        if ($this->paymentParams)
        {
            $importRes = dom_import_simplexml($result);
            $importPaymentParams = dom_import_simplexml($this->paymentParams->asXml());
            $importRes->appendChild($importRes->ownerDocument->importNode($importPaymentParams, true));
        }

        return $result;
    }

    public function toXml()
    {
        return $this->asXml()->asXML();
    }

    public static function getAccountByMobileOperator( $operator )
    {
        switch($operator) {
            case self::MOBILE_OPERATOR_MTS:
                return self::ACCOUNT_MTS;
                break;
            case self::MOBILE_OPERATOR_TELE2:
                return self::ACCOUNT_TELE2;
                break;
            case self::MOBILE_OPERATOR_MEGAFON:
                return self::ACCOUNT_MEGAFON;
                break;
            case self::MOBILE_OPERATOR_BILINE:
                return self::ACCOUNT_BILINE;
                break;
            default:
                throw new \Exception('Invalid mobile operator type.');

        }
    }
}