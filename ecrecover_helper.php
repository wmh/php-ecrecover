<?php
require_once './CryptoCurrencyPHP/PointMathGMP.class.php';
require_once './CryptoCurrencyPHP/SECp256k1.class.php';
require_once './CryptoCurrencyPHP/Signature.class.php';
require_once './vendor/autoload.php';
use kornrunner\Keccak;

function personal_ecRecover($msg, $signed) {
    $personal_prefix_msg = "\x19Ethereum Signed Message:\n". strlen($msg). $msg;
    $hex = keccak256($personal_prefix_msg);
    return ecRecover($hex, $signed);
}

function ecRecover($hex, $signed) {
    $rHex   = substr($signed, 2, 64);
    $sHex   = substr($signed, 66, 64);
    $vValue = hexdec(substr($signed, 130, 2));
    $messageHex       = substr($hex, 2);
    $messageByteArray = unpack('C*', hex2bin($messageHex));
    $messageGmp       = gmp_init("0x" . $messageHex);
    $r = $rHex;		//hex string without 0x
    $s = $sHex; 	//hex string without 0x
    $v = $vValue; 	//27 or 28

    //with hex2bin it gives the same byte array as the javascript
    $rByteArray = unpack('C*', hex2bin($r));
    $sByteArray = unpack('C*', hex2bin($s));
    $rGmp = gmp_init("0x" . $r);
    $sGmp = gmp_init("0x" . $s);

    if ($v != 27 && $v != 28) {
        $v += 27;
    }

    $recovery = $v - 27;
    if ($recovery !== 0 && $recovery !== 1) {
        throw new Exception('Invalid signature v value');
    }

    $publicKey = Signature::recoverPublicKey($rGmp, $sGmp, $messageGmp, $recovery);
    $publicKeyString = $publicKey["x"] . $publicKey["y"];

    return '0x'. substr(keccak256(hex2bin($publicKeyString)), -40);
}

function strToHex($string)
{
    $hex = unpack('H*', $string);
    return '0x' . array_shift($hex);
}

function keccak256($str) {
    return '0x'. Keccak::hash($str, 256);
}
