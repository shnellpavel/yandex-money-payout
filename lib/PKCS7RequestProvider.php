<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 19:31
 */

namespace YandexMoney;


use YandexMoney\interfaces\IDispositionRequestProvider;
use YandexMoney\interfaces\IXMLTransformable;

class PKCS7RequestProvider implements IDispositionRequestProvider
{
    private $settings;

    public function __construct( Settings $settings )
    {
        $this->settings = $settings;
    }

    public function processRequest( $handler )
    {
        if ( ( $request = $this->verifyData( file_get_contents( "php://input" ) ) ) == null )
        {
            /**
             * @var IXMLTransformable $params
             */
            $params = call_user_func($handler, $request);
            header( "HTTP/1.0 200" );
            header( "Content-Type: application/pkcs7-mime" );
            echo $this->signData( $params->toXml() );
            exit;
        }
    }

    public function sendRequest( $dispositionMethod, IXMLTransformable $params )
    {
        $curl   = curl_init();
        $params = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => array( 'Content-type: application/pkcs7-mime' ),
            CURLOPT_URL            => rtrim( $this->settings->host, '/' ) . '/webservice/deposition/api/' . trim( $dispositionMethod, '/' ),
            CURLOPT_POST           => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSLCERT        => $this->settings->cert,
            CURLOPT_SSLKEY         => $this->settings->privateKey,
            CURLOPT_SSLCERTPASSWD  => $this->settings->certPassword,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE        => 0,
            CURLOPT_POSTFIELDS     => $this->signData( $params->toXml() )
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
        } catch ( \HttpException $ex )
        {
            echo $ex;
        }

        return $this->verifyData( $result );
    }

    private function signData( $data )
    {
        $descriptorSpec      = array(
            0 => array( "pipe", "r" ),
            1 => array( "pipe", "w" ),
        );
        $descriptorSpec[ 2 ] = $descriptorSpec[ 1 ];
        try
        {
            $opensslCommand = 'openssl smime -sign -signer ' . $this->settings->cert .
                ' -inkey ' . $this->settings->privateKey .
                ' -nochain -nocerts -outform PEM -nodetach -passin pass:' . $this->settings->certPassword;

            $process = proc_open( $opensslCommand, $descriptorSpec, $pipes );
            if ( is_resource( $process ) )
            {
                fwrite( $pipes[ 0 ], $data );
                fclose( $pipes[ 0 ] );
                $signedData = stream_get_contents( $pipes[ 1 ] );
                fclose( $pipes[ 1 ] );
                $resCode = proc_close( $process );
                if ( $resCode != 0 )
                {
                    $errorMsg = 'OpenSSL call failed:' . $resCode . '\n' . $signedData;
                    throw new \Exception( $errorMsg );
                }
                return $signedData;
            }
        } catch ( \Exception $e )
        {
            throw $e;
        }
    }

    private function verifyData( $data )
    {
        $descriptorSpec = array(
            0 => array( "pipe", "r" ),
            1 => array( "pipe", "w" ),
            2 => array( "pipe", "w" )
        );
        $verifyCommand  = 'openssl smime -verify -inform PEM -nointern' .
            ' -certfile ' . $this->settings->yaCert .
            ' -CAfile ' . $this->settings->yaCert;

        $process = proc_open( $verifyCommand, $descriptorSpec, $pipes );
        if ( is_resource( $process ) )
        {
            fwrite( $pipes[ 0 ], $data );
            fclose( $pipes[ 0 ] );
            $content = stream_get_contents( $pipes[ 1 ] );
            fclose( $pipes[ 1 ] );
            $resCode = proc_close( $process );

            if ( $resCode != 0 )
            {
                return null;
            }
            else
            {
                $xml   = simplexml_load_string( $content );
                $array = json_decode( json_encode( $xml ), true );
                return $array[ "@attributes" ];
            }
        }
        return null;
    }
}