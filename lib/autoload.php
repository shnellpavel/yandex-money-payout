<?php
/**
 * User: shnell
 * Date: 25.04.16
 * Time: 19:33
 */

function __autoload( $className )
{
    $parts = explode( '\\', $className );

    if ( $parts[ 0 ] == 'YandexMoney' )
    {
        require __DIR__.'/'.implode('/', array_slice($parts, 1)) . '.php';
    }
}

spl_autoload_register( '__autoload' );