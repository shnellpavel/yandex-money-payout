<?php
use YandexMoney\DepositionRequestParams;

/**
 * User: shnell
 * Date: 27.04.16
 * Time: 11:08
 */

class DepositionRequestParamsTest extends PHPUnit_Framework_TestCase {

    public function testToXml()
    {
        $agentId = '123';
        $orderId = '456';
        $params = new DepositionRequestParams($agentId, $orderId, 'testDeposition');
        $xml = $params->toXml();
        $xmlElem = simplexml_load_string($xml);
        $attrs = $xmlElem->attributes();
        $this->assertEquals($agentId, $attrs['agentId']);
        $this->assertEquals($orderId, $attrs['clientOrderId']);
        $this->assertNotEmpty($attrs['requestDT']);
    }
}
 