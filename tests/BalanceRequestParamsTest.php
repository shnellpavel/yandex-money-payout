<?php
use YandexMoney\BalanceRequestParams;

/**
 * User: shnell
 * Date: 27.04.16
 * Time: 1:45
 */

class BalanceRequestParamsTest extends PHPUnit_Framework_TestCase {

    public function testToXml()
    {
        $agentId = '123';
        $orderId = '456';
        $params = new BalanceRequestParams($agentId, $orderId);
        $xml = $params->toXml();
        $xmlElem = simplexml_load_string($xml);
        $attrs = $xmlElem->attributes();
        $this->assertEquals($agentId, $attrs['agentId']);
        $this->assertEquals($orderId, $attrs['clientOrderId']);
        $this->assertNotEmpty($attrs['requestDT']);
    }
}
 