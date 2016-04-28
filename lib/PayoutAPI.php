<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 18:35
 */

namespace YandexMoney;


use YandexMoney\interfaces\IDispositionRequestProvider;
use YandexMoney\interfaces\IPayoutAPI;

class PayoutAPI implements IPayoutAPI
{
    const REQ_STATUS_SUCCESS     = 0;
    const REQ_STATUS_IN_PROGRESS = 1;
    const REQ_STATUS_REJECTED    = 3;

    const ERROR_SYNTAX                     = 10;
    const ERROR_AGENT_ID                   = 11;
    const ERROR_SUBAGENT_ID                = 12;
    const ERROR_CURRENCY                   = 14;
    const ERROR_REQUEST_DT                 = 15;
    const ERROR_DST_ACCOUNT                = 16;
    const ERROR_AMOUNT                     = 17;
    const ERROR_CLIENT_ORDER_ID            = 18;
    const ERROR_CONTRACT                   = 19;
    const ERROR_FORBIDDEN_OPERATION        = 21;
    const ERROR_NOT_UNIQUE_CLIENT_ORDER_ID = 26;
    const ERROR_BROKEN_PACK                = 50;
    const ERROR_INVALID_SIGNATURE          = 51;
    const ERROR_UNKNOWN_SIGN_CERT          = 53;
    const ERROR_EXPIRED_CERT               = 55;
    const ERROR_ACCOUNT_CLOSED             = 40;
    const ERROR_LOCKED_YA_WALLET           = 41;
    const ERROR_UNKNOWN_ACCOUNT            = 42;
    const ERROR_ONCE_LIMIT                 = 43;
    const ERROR_PERIOD_LIMIT               = 44;
    const ERROR_SMALL_BALANCE              = 45;
    const ERROR_AMOUNT_TOO_SMALL           = 46;
    const ERROR_DEPOSITION_REQUEST         = 48;
    const ERROR_LIMIT_RECEIVER_BALANCE     = 201;
    const ERROR_YANDEX_ERROR               = 30;
    const ERROR_RECEIVER_REJECT_DEPOSITION = 31;
    const ERROR_PAYMENT_EXPIRED_TIME       = 105;
    const ERROR_RECEIVER_PAYMENT_REVERT    = 110;

    private $requestProvider;
    private $synonimUrl;

    public function __construct( IDispositionRequestProvider $requestProvider, $synonimUrl )
    {
        $this->requestProvider = $requestProvider;
        $this->synonimUrl      = $synonimUrl;
    }

    public function getCardSynonim( $cardNum )
    {
        $curl   = curl_init();
        $params = array(
            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_HTTPHEADER     => array( 'Content-type: application/pkcs7-mime' ),
            CURLOPT_URL            => $this->synonimUrl,
            CURLOPT_POST           => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE        => 0,
            CURLOPT_POSTFIELDS     => http_build_query(array(
                'skr_destinationCardNumber' => $cardNum,
                'skr_responseFormat'        => 'json',
            ))
        );
        curl_setopt_array( $curl, $params );

        $result = null;
        try
        {
            $result = curl_exec( $curl );
            if ( !$result )
            {
                trigger_error( curl_error( $curl ) );
            }
            curl_close( $curl );

            $result = (array)json_decode( $result );
            $result = $result[ 'storeCard' ];
        } catch ( \HttpException $ex )
        {
            echo $ex;
        }

        return $result;
    }

    public function makeDeposition( DepositionRequestParams $params )
    {
        return $this->requestProvider->sendRequest( 'makeDeposition', $params );
    }

    public function testDeposition( DepositionRequestParams $params )
    {
        return $this->requestProvider->sendRequest( 'testDeposition', $params );
    }

    public function getBalance( BalanceRequestParams $params )
    {
        return $this->requestProvider->sendRequest( 'balance', $params );
    }

    public function errorDepositionNotification( $handler )
    {
        return $this->requestProvider->processRequest( $handler );
    }

    public static function translateError( $code )
    {
        $translations = array(
            self::ERROR_SYNTAX                     => 'Ошибка синтаксического разбора XML-документа. Синтаксис документа нарушен или отсутствуют обязательные элементы XML.',
            self::ERROR_AGENT_ID                   => 'Отсутствует или неверно задан идентификатор контрагента (agentId).',
            self::ERROR_SUBAGENT_ID                => 'Отсутствует или неверно задан идентификатор канала приема переводов (subagentId).',
            self::ERROR_CURRENCY                   => 'Отсутствует или неверно задана валюта (currency).',
            self::ERROR_REQUEST_DT                 => 'Отсутствует или неверно задано время формирования документа (requestDT).',
            self::ERROR_DST_ACCOUNT                => 'Отсутствует или неверно задан идентификатор получателя средств (dstAccount).',
            self::ERROR_AMOUNT                     => 'Отсутствует или неверно задана сумма (amount).',
            self::ERROR_CLIENT_ORDER_ID            => 'Отсутствует или неверно задан номер транзакции (clientOrderId).',
            self::ERROR_CONTRACT                   => 'Отсутствует или неверно задано основание для зачисления перевода (contract).',
            self::ERROR_FORBIDDEN_OPERATION        => 'Запрашиваемая операция запрещена для данного типа подключения контрагента.',
            self::ERROR_NOT_UNIQUE_CLIENT_ORDER_ID => 'Операция с таким номером транзакции (clientOrderId), но другими параметрами уже выполнялась.',
            self::ERROR_BROKEN_PACK                => 'Невозможно открыть криптосообщение, ошибка целостности пакета.',
            self::ERROR_INVALID_SIGNATURE          => 'АСП не подтверждена (данные подписи не совпадают с документом).',
            self::ERROR_UNKNOWN_SIGN_CERT          => 'Запрос подписан неизвестным Яндекс.Деньгам сертификатом.',
            self::ERROR_EXPIRED_CERT               => 'Истек срок действия сертификата в системе контрагента.',
            self::ERROR_ACCOUNT_CLOSED             => 'Счет закрыт.',
            self::ERROR_LOCKED_YA_WALLET           => 'Кошелек в Яндекс.Деньгах заблокирован. Данная операция для этого кошелька запрещена.',
            self::ERROR_UNKNOWN_ACCOUNT            => 'Счета с таким идентификатором не существует.',
            self::ERROR_ONCE_LIMIT                 => 'Превышено ограничение на единовременно зачисляемую сумму.',
            self::ERROR_PERIOD_LIMIT               => 'Превышено ограничение на максимальную сумму зачислений за период времени.',
            self::ERROR_SMALL_BALANCE              => 'Недостаточно средств для проведения операции.',
            self::ERROR_AMOUNT_TOO_SMALL           => 'Сумма операции слишком мала.',
            self::ERROR_DEPOSITION_REQUEST         => 'Ошибка запроса зачисления перевода на банковский счет, карту, мобильный телефон.',
            self::ERROR_LIMIT_RECEIVER_BALANCE     => 'Превышен лимит остатка на счете получателя.',
            self::ERROR_YANDEX_ERROR               => 'Технические проблемы на стороне Яндекс.Денег.',
            self::ERROR_RECEIVER_REJECT_DEPOSITION => 'Получатель перевода отклонил платеж (под получателем понимается сотовый оператор или процессинговый банк).',
            self::ERROR_PAYMENT_EXPIRED_TIME       => 'Превышено допустимое время оплаты по данному коду платежа (при оплате наличными через терминалы, салоны связи и пр.).',
            self::ERROR_RECEIVER_PAYMENT_REVERT    => 'Получатель перевода вернул платеж (под получателем понимается сотовый оператор или процессинговый банк).',
        );

        return ( ( isset( $translations[ $code ] ) ) ? $translations[ $code ] : $code );
    }
}