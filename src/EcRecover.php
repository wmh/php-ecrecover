<?php

declare(strict_types=1);

namespace Wmh\EcRecover;

use kornrunner\Keccak;

/**
 * Ethereum Signature Recovery Library
 * 
 * Recovers Ethereum addresses from signed messages using ECDSA signature recovery.
 */
class EcRecover
{
    /**
     * Recover Ethereum address from a personal_sign signature
     * 
     * @param string $message The original message that was signed
     * @param string $signature The signature hex string (with or without 0x prefix)
     * @return string The recovered Ethereum address (with 0x prefix)
     * @throws \Exception If signature is invalid
     */
    public static function personalRecover(string $message, string $signature): string
    {
        $personalPrefixMsg = "\x19Ethereum Signed Message:\n" . strlen($message) . $message;
        $hex = self::keccak256($personalPrefixMsg);
        return self::recover($hex, $signature);
    }

    /**
     * Recover Ethereum address from a signature
     * 
     * @param string $messageHash The message hash (with or without 0x prefix)
     * @param string $signature The signature hex string (with or without 0x prefix)
     * @return string The recovered Ethereum address (with 0x prefix)
     * @throws \Exception If signature is invalid
     */
    public static function recover(string $messageHash, string $signature): string
    {
        // Remove 0x prefix if present
        $signature = str_starts_with($signature, '0x') ? substr($signature, 2) : $signature;
        $messageHash = str_starts_with($messageHash, '0x') ? substr($messageHash, 2) : $messageHash;

        // Parse signature components
        $r = substr($signature, 0, 64);
        $s = substr($signature, 64, 64);
        $v = hexdec(substr($signature, 128, 2));

        // Convert to GMP integers
        $messageGmp = gmp_init('0x' . $messageHash);
        $rGmp = gmp_init('0x' . $r);
        $sGmp = gmp_init('0x' . $s);

        // Normalize v value (should be 27 or 28)
        if ($v !== 27 && $v !== 28) {
            $v += 27;
        }

        $recovery = $v - 27;
        if ($recovery !== 0 && $recovery !== 1) {
            throw new \Exception('Invalid signature v value');
        }

        // Recover public key
        $publicKey = Signature::recoverPublicKey($rGmp, $sGmp, $messageGmp, $recovery);
        $publicKeyString = $publicKey['x'] . $publicKey['y'];

        // Return Ethereum address (last 20 bytes of keccak256 hash of public key)
        return '0x' . substr(self::keccak256(hex2bin($publicKeyString)), -40);
    }

    /**
     * Recover address from EIP-712 typed data signature
     * 
     * @param string $typeHash The type hash
     * @param string $dataHash The data hash
     * @param string $signature The signature hex string
     * @return string The recovered Ethereum address
     * @throws \Exception If signature is invalid
     */
    public static function recoverTypedData(string $typeHash, string $dataHash, string $signature): string
    {
        $preHashStr = hex2bin(substr($typeHash, 2) . substr($dataHash, 2));
        $hex = self::keccak256($preHashStr);
        return self::recover($hex, $signature);
    }

    /**
     * Calculate Keccak256 hash
     * 
     * @param string $data The data to hash
     * @return string The hash with 0x prefix
     */
    public static function keccak256(string $data): string
    {
        return '0x' . Keccak::hash($data, 256);
    }

    /**
     * Convert string to hex
     * 
     * @param string $string The string to convert
     * @return string The hex string with 0x prefix
     */
    public static function strToHex(string $string): string
    {
        return '0x' . bin2hex($string);
    }
}
