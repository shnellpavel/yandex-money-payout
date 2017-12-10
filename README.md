# Библиотека для интеграции с Массовыми выплатами от Яндекс

## Описание
* О функционале выплат можно прочить сайте [Яндекс.Касса](https://kassa.yandex.ru/payouts)
* Документация по интеграции может быть найдена на по ссылке выше или [здесь](https://tech.yandex.ru/money/doc/payment-solution/payout/intro-docpage/) 

## Реализация
* Получение баланса
* Проверка возможности осуществить перевод
* Перевод на Яндекс кошелёк
* Перевод на счёт мобильного телефона
* Перевод на банковскую карту

## Пример использования:
```php
<?php

$settings               = new \YandexMoney\Settings();
$settings->host         = $params['yandexPayout']['host'];
$settings->cert         = $params['yandexPayout']['cert'];
$settings->certPassword = $params['yandexPayout']['certPassword'];
$settings->privateKey   = $params['yandexPayout']['privateKey'];
$settings->yaCert       = $params['yandexPayout']['yaCert'];
$provider               = new \YandexMoney\PKCS7RequestProvider($settings);

$api = new \YandexMoney\PayoutAPI($provider, $params['yandexPayout']['cardSynonimUrl']);

// Обработка перевода
$depositionParams             = new \YandexMoney\DepositionRequestParams($agentId, $clientOrderId,
    'makeDeposition');
$depositionParams->amount     = $amount;
$depositionParams->dstAccount = $dstAccount;
$depositionParams->currency   = $currency;
$depositionParams->contract   = $contract;

if ($depositionType == TYPE_MOBILE) {
    $paymentParams               = new \YandexMoney\MobilePaymentParams();
    $paymentParams->operatorCode = $phoneOperatorCode;
    $paymentParams->phoneNumber  = $phoneNumber;
    $depositionParams->setPaymentParams($paymentParams);
} elseif ($depositionType == TYPE_BANK_CARD) {
    // Получаем синоним и маску
    $synonimRes = $api->getCardSynonim($cardNumber);
    if ($synonimRes != null) {
        $cardSynonim = $synonimRes['skr_destinationCardSynonim'];
        $cardMask    = $synonimRes['skr_destinationCardPanmask'];
    }
    
    $paymentParams              = new \YandexMoney\BankCardPaymentParams();
    $paymentParams->cardSynonim = $cardSynonim;

    $paymentParams->lastName   = $payerLastName;
    $paymentParams->firstName  = $payerFirstName;
    $paymentParams->middleName = $payerMiddleName;

    $paymentParams->birthDate  = $payerBirthDate;
    $paymentParams->birthPlace = $payerBirthPlace;
    $paymentParams->address    = $payerAddress;
    $paymentParams->city       = $payerCity;
    $paymentParams->country    = $payerCountry;
    $paymentParams->postcode   = $payerPostcode;

    $paymentParams->docNumber      = $payerDocNumber;
    $paymentParams->docIssueDate   = $payerDocIssueDate;
    $paymentParams->docIssuedBy    = $payerDocIssuedBy;
    $paymentParams->smsPhoneNumber = $smsPhoneNumber;

    $depositionParams->setPaymentParams($paymentParams);
} else {
    // Перевод на Яндекс кошелёк, уточненять параметры перевода не требуется
}

$response = $api->makeDeposition($depositionParams);

switch (intval($response['status'])) {
    case \YandexMoney\PayoutAPI::REQ_STATUS_SUCCESS:
        // ...
        break;
    case \YandexMoney\PayoutAPI::REQ_STATUS_IN_PROGRESS:
        // ...
        break;
    case \YandexMoney\PayoutAPI::REQ_STATUS_REJECTED:
        $error = $response['error'];
        // ...
        break;
}
```