<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 19:30
 */

namespace YandexMoney\interfaces;


interface IDispositionRequestProvider {
    public function sendRequest($dispositionMethod, IXMLTransformable $params);
    public function processRequest($handler);
} 